<?php

namespace App\Modules\Post\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePostRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'title'          => ['nullable', 'string', 'max:500'],
            'content'        => ['nullable', 'string'],
            'media'          => ['nullable', 'file', 'mimes:jpg,jpeg,png,gif,mp4,mov', 'max:51200'],
            'external_link'  => ['nullable', 'url', 'max:500'],
            'visibility'     => ['required', 'in:public,private'],
            'post_type'      => ['required', 'in:post,achievement,project,assignment'],
            'tags'           => ['nullable', 'array'],
            'tags.*'         => ['string', 'max:50'],
            'page_id'        => ['nullable', 'exists:pages,id'],
            'parent_post_id' => ['nullable', 'exists:posts,id'],
            'scheduled_at'   => ['nullable', 'date', 'after:now'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($v) {
            if (!$this->filled('title') && !$this->filled('content') && !$this->hasFile('media')) {
                $v->errors()->add('content', 'Bài viết phải có nội dung, tiêu đề hoặc file đính kèm.');
            }
        });
    }
}