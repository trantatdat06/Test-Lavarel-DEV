<?php

namespace App\Modules\Form\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Form;
use App\Models\FormSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Gate;

class FormController extends Controller
{
    /**
     * HIỂN THỊ GIAO DIỆN TẠO FORM
     */
    public function create()
    {
        return view('form.create');
    }

    /**
     * TẠO FORM MỚI (Dành cho Admin/Ban tổ chức)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'event_id' => 'nullable|exists:events,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'fields' => 'required|array|min:1',
            'fields.*.label' => 'required|string',
            'fields.*.type' => 'required|in:text,textarea,select,radio,checkbox,date,file,email,phone',
            'fields.*.required' => 'boolean',
            'fields.*.options' => 'nullable|array',
            'start_time'  => 'nullable|date',
            'end_time'    => 'nullable|date|after_or_equal:start_time',
            'tags'        => 'nullable|array',
            'tags.*'      => 'string|exists:tags,name'
        ]);

        $form = DB::transaction(function () use ($validated, $request) {
            $newForm = Form::create([
                'event_id' => $validated['event_id'] ?? null,
                'title' => $validated['title'],
                'description' => $validated['description'],
                'start_time'  => $validated['start_time'] ?? null, 
                'end_time'    => $validated['end_time'] ?? null,
                'tags'        => $request->input('tags', []),
            ]);

            foreach ($validated['fields'] as $index => $fieldData) {
                $newForm->fields()->create([
                    'label' => $fieldData['label'],
                    'type' => $fieldData['type'],
                    'required' => $fieldData['required'] ?? false,
                    'options' => $fieldData['options'] ?? null,
                    'order' => $index,
                ]);
            }
            
            return $newForm; 
        });

        return redirect()->route('forms.show', $form->id)
                         ->with('success', 'Tạo biểu mẫu thành công! Bạn có thể copy đường link này để chia sẻ.');
    }

    /**
     * HIỂN THỊ TRANG CHI TIẾT CỦA FORM 
     */
    public function show(Form $form)
    {
        $form->load('fields');
        return view('form.show', compact('form'));
    }

    /**
     * XỬ LÝ DỮ LIỆU NGƯỜI DÙNG NỘP LÊN (DYNAMIC VALIDATION)
     */
    public function submit(Request $request, Form $form)
    {
        $form->load('fields');

        $rules = [];
        $messages = [];

        foreach ($form->fields as $field) {
            $fieldName = 'field_' . $field->id; 
            $fieldRules = [];

            if ($field->required) {
                $fieldRules[] = 'required';
                $messages["{$fieldName}.required"] = "Trường '{$field->label}' không được bỏ trống.";
            } else {
                $fieldRules[] = 'nullable';
            }

            if ($field->type === 'email') {
                $fieldRules[] = 'email';
                $messages["{$fieldName}.email"] = "Trường '{$field->label}' phải là định dạng email hợp lệ.";
            }
            if ($field->type === 'file') {
                $fieldRules[] = 'file|max:5120';
                $messages["{$fieldName}.max"] = "File tải lên ở mục '{$field->label}' không được vượt quá 5MB.";
            }

            $rules[$fieldName] = implode('|', $fieldRules);
        }

        $validator = Validator::make($request->all(), $rules, $messages);
        
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $validatedData = $validator->validated();

        foreach ($form->fields as $field) {
            $fieldName = 'field_' . $field->id;
            if ($field->type === 'file' && $request->hasFile($fieldName)) {
                $validatedData[$fieldName] = $request->file($fieldName)->store('form_submissions', 'public');
            }
        }

        // --- BẮT ĐẦU ĐOẠN HACK TEST CHO AUTO-FILL ---
        $testUser = $request->user() ?? \App\Models\User::first();

        // GHI ĐÈ LẠI DỮ LIỆU ĐÃ MAP TỪ PROFILE USER
        foreach ($form->fields as $field) {
            $fieldName = 'field_' . $field->id;
            
            if ($field->mapping_key === 'student_code') {
                $validatedData[$fieldName] = $testUser->student_code;
            } elseif ($field->mapping_key === 'full_name') {
                $validatedData[$fieldName] = $testUser->display_name;
            } elseif ($field->mapping_key === 'email') {
                $validatedData[$fieldName] = $testUser->email;
            }
        }

        FormSubmission::create([
            'form_id'      => $form->id,
            'user_id'      => $testUser ? $testUser->id : null, 
            'data'         => $validatedData,
            'status'       => 'pending',
            'submitted_at' => now(),
        ]);
        // --- KẾT THÚC ĐOẠN HACK ---

        return back()->with('success', 'Đã gửi thông tin liên hệ thành công! Chúng tôi sẽ phản hồi sớm nhất.');
    }

    /**
     * XEM DANH SÁCH CÁC ĐƠN ĐÃ NỘP (Dành cho Admin duyệt đơn)
     */
    public function submissions(Form $form)
    {
        $submissions = $form->submissions()->with('user')->latest()->paginate(30);
        return view('form.submissions', compact('form', 'submissions'));
    }

    /**
     * CẬP NHẬT TRẠNG THÁI ĐƠN NỘP (Duyệt/Từ chối)
     */
    public function updateSubmission(Request $request, FormSubmission $submission)
    {
        $validated = $request->validate([
            'status' => 'required|in:approved,rejected,cancelled',
            'note'   => 'nullable|string'
        ]);

        $submission->update([
            'status' => $validated['status'],
            'note'   => $validated['note'] ?? $submission->note,
        ]);

        $message = $validated['status'] == 'approved' 
                   ? "Đơn đăng ký của bạn đã được duyệt." 
                   : "Đơn của bạn bị từ chối với lý do: " . ($validated['note'] ?? 'Không có');

        \App\Models\Notification::create([
            'user_id' => $submission->user_id,
            'content' => $message,
            'type'    => 'FORM_STATUS_UPDATE'
        ]);

        return back()->with('success', 'Đã cập nhật trạng thái đơn thành công!');
    }

    /**
     * XUẤT DỮ LIỆU ĐƠN RA FILE EXCEL (CSV)
     */
    public function export(Form $form)
    {
        $submissions = $form->submissions()->with('user')->get();
        $form->load('fields');

        $fileName = "Danh_sach_don_" . \Str::slug($form->title) . "_" . date('Y-m-d') . ".csv";

        $headers = array(
            "Content-type"        => "text/csv; charset=utf-8",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        $callback = function() use($submissions, $form) {
            $file = fopen('php://output', 'w');
            fputs($file, $bom =(chr(0xEF) . chr(0xBB) . chr(0xBF)));

            $columns = ['ID', 'Thời gian nộp', 'Trạng thái', 'Mã Sinh Viên', 'Họ và Tên', 'Email'];
            foreach ($form->fields as $field) {
                $columns[] = $field->label;
            }
            fputcsv($file, $columns);

            foreach ($submissions as $sub) {
                $statusMap = [
                    'pending' => 'Chờ duyệt',
                    'approved' => 'Đã duyệt',
                    'rejected' => 'Từ chối',
                    'cancelled' => 'Đã hủy'
                ];

                $row = [
                    $sub->id,
                    \Carbon\Carbon::parse($sub->submitted_at)->format('H:i d/m/Y'),
                    $statusMap[$sub->status] ?? $sub->status,
                    $sub->user ? $sub->user->student_code : 'Khách',
                    $sub->user ? $sub->user->display_name : 'Khách',
                    $sub->user ? $sub->user->email : 'N/A',
                ];

                foreach ($form->fields as $field) {
                    $fieldName = 'field_' . $field->id;
                    $value = $sub->data[$fieldName] ?? '';
                    
                    if (is_array($value)) {
                        $value = implode(', ', $value);
                    } elseif (is_string($value) && str_starts_with($value, 'form_submissions/')) {
                        $value = asset('storage/' . $value); 
                    }
                    $row[] = $value;
                }
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}