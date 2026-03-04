<?php

namespace App\Modules\Form\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Form;
use App\Models\FormSubmission;
use Illuminate\Http\Request;

class FormController extends Controller
{
    public function show(Form $form)
    {
        $this->authorize('view', $form);
        $form->load('fields');
        return view('form.show', compact('form'));
    }

    public function submit(Request $request, Form $form)
    {
        $this->authorize('submit', $form);

        $data = $request->except(['_token', '_method']);

        FormSubmission::create([
            'form_id'      => $form->id,
            'user_id'      => $request->user()->id,
            'data'         => $data,
            'status'       => 'pending',
            'submitted_at' => now(),
        ]);

        return back()->with('success', 'Đã gửi phiếu đăng ký! Vui lòng chờ xác nhận.');
    }

    public function submissions(Form $form)
    {
        $this->authorize('update', $form);

        $submissions = $form->submissions()->with('user')->latest()->paginate(30);

        return view('form.submissions', compact('form', 'submissions'));
    }

    public function updateSubmission(Request $request, FormSubmission $submission)
    {
        $this->authorize('update', $submission->form);

        $submission->update([
            'status' => $request->validate(['status' => 'required|in:approved,rejected,cancelled'])['status'],
            'note'   => $request->input('note'),
        ]);

        return back()->with('success', 'Đã cập nhật trạng thái.');
    }
}