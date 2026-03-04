<?php

namespace App\Modules\Post\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Post\Jobs\ImportPostsJob;
use Illuminate\Http\Request;

class BulkPostController extends Controller
{
    /**
     * Import posts from Excel and dispatch jobs to queue.
     * Supports scheduling via scheduled_at column in the file.
     */
    public function import(Request $request)
    {
        $request->validate([
            'file'    => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:10240'],
            'page_id' => ['nullable', 'exists:pages,id'],
        ]);

        $path = $request->file('file')->store('bulk-imports', 'local');

        ImportPostsJob::dispatch(
            filePath: $path,
            userId:   $request->user()->id,
            pageId:   $request->integer('page_id', 0) ?: null,
        );

        return back()->with('success', 'File đã được tải lên và đang được xử lý. Bài viết sẽ được đăng tự động.');
    }
}