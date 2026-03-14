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
        ]);

        // 1. Gán kết quả của DB::transaction vào biến $form để lấy được ID của form vừa tạo
        $form = DB::transaction(function () use ($validated) {
            $newForm = Form::create([
                'event_id' => $validated['event_id'] ?? null,
                'title' => $validated['title'],
                'description' => $validated['description'],
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
            
            return $newForm; // Trả dữ liệu ra ngoài vòng lặp
        });

        // 2. Thay vì return response()->json(), ta Redirect người dùng sang trang xem Form
        return redirect()->route('forms.show', $form->id)
                         ->with('success', 'Tạo biểu mẫu thành công! Bạn có thể copy đường link này để chia sẻ.');
    }

    /**
     * HIỂN THỊ TRANG CHI TIẾT CỦA FORM 
     */
    public function show(Form $form)
    {
        // Đã tắt kiểm tra quyền để khách vãng lai cũng xem được form
        // $this->authorize('view', $form);
        
        $form->load('fields');
        
        return view('form.show', compact('form'));
    }

    /**
     * XỬ LÝ DỮ LIỆU NGƯỜI DÙNG NỘP LÊN (DYNAMIC VALIDATION)
     */
    public function submit(Request $request, Form $form)
    {
        // Đã tắt kiểm tra quyền để khách vãng lai cũng nộp được form
        // $this->authorize('submit', $form);
        
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

        FormSubmission::create([
            'form_id'      => $form->id,
            // Sửa lại dòng này: Nếu có User đang đăng nhập thì lấy ID, nếu là khách thì để null
            'user_id'      => $request->user() ? $request->user()->id : null, 
            'data'         => $validatedData,
            'status'       => 'pending',
            'submitted_at' => now(),
        ]);

        return back()->with('success', 'Đã gửi thông tin liên hệ thành công! Chúng tôi sẽ phản hồi sớm nhất.');
    }

    /**
     * XEM DANH SÁCH CÁC ĐƠN ĐÃ NỘP (Dành cho Admin duyệt đơn)
     */
    public function submissions(Form $form)
    {
        // Sử dụng Gate thay vì $this->authorize cho bản Laravel 12
        //Gate::authorize('update', $form);

        $submissions = $form->submissions()->with('user')->latest()->paginate(30);

        return view('form.submissions', compact('form', 'submissions'));
    }

    /**
     * CẬP NHẬT TRẠNG THÁI ĐƠN NỘP (Duyệt/Từ chối)
     */
    public function updateSubmission(Request $request, FormSubmission $submission)
    {
        //Gate::authorize('update', $submission->form);

        $validated = $request->validate([
            'status' => 'required|in:approved,rejected,cancelled',
            'note'   => 'nullable|string'
        ]);

        $submission->update([
            'status' => $validated['status'],
            'note'   => $validated['note'] ?? $submission->note,
        ]);

        return back()->with('success', 'Đã cập nhật trạng thái đơn thành công!');
    }

    /**
     * XUẤT DỮ LIỆU ĐƠN RA FILE EXCEL (CSV)
     */
    public function export(Form $form)
    {
        // Lấy tất cả đơn nộp kèm theo thông tin User (Tài khoản sinh viên)
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
            
            // Fix lỗi font tiếng Việt khi mở bằng Excel (BOM)
            fputs($file, $bom =(chr(0xEF) . chr(0xBB) . chr(0xBF)));

            // 1. Tạo Dòng Tiêu Đề Cột
            $columns = ['ID', 'Thời gian nộp', 'Trạng thái', 'Mã Sinh Viên', 'Họ và Tên', 'Email'];
            foreach ($form->fields as $field) {
                $columns[] = $field->label;
            }
            fputcsv($file, $columns);

            // 2. Điền Dữ Liệu Từng Dòng
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
                    // Lấy thông tin từ bảng User (Auto-fill)
                    $sub->user ? $sub->user->student_code : 'Khách (Không đăng nhập)',
                    $sub->user ? $sub->user->display_name : 'Khách',
                    $sub->user ? $sub->user->email : 'N/A',
                ];

                // Đổ dữ liệu các câu hỏi tùy chọn
                foreach ($form->fields as $field) {
                    $fieldName = 'field_' . $field->id;
                    $value = $sub->data[$fieldName] ?? '';
                    
                    if (is_array($value)) {
                        $value = implode(', ', $value);
                    } elseif (is_string($value) && str_starts_with($value, 'form_submissions/')) {
                        $value = asset('storage/' . $value); // Biến file ảnh thành link để bấm vào xem được
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