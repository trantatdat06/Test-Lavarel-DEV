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

        DB::transaction(function () use ($validated) {
            $form = Form::create([
                'event_id' => $validated['event_id'] ?? null,
                'title' => $validated['title'],
                'description' => $validated['description'],
            ]);

            foreach ($validated['fields'] as $index => $fieldData) {
                $form->fields()->create([
                    'label' => $fieldData['label'],
                    'type' => $fieldData['type'],
                    'required' => $fieldData['required'] ?? false,
                    'options' => $fieldData['options'] ?? null,
                    'order' => $index,
                ]);
            }
        });

        return response()->json(['message' => 'Tạo biểu mẫu thành công!']);
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
}