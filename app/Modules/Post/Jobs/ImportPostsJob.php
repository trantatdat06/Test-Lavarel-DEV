<?php

namespace App\Modules\Post\Jobs;

use App\Models\Post;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ImportPostsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(
        public string $filePath,
        public int    $userId,
        public ?int   $pageId = null,
    ) {}

    public function handle(): void
    {
        $fullPath = Storage::disk('local')->path($this->filePath);
        $spreadsheet = IOFactory::load($fullPath);
        $rows = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

        // Skip header row
        array_shift($rows);

        foreach ($rows as $row) {
            $title       = $row['A'] ?? null;
            $content     = $row['B'] ?? null;
            $visibility  = in_array($row['C'] ?? 'public', ['public', 'private']) ? ($row['C'] ?? 'public') : 'public';
            $scheduled   = !empty($row['D']) ? \Carbon\Carbon::parse($row['D']) : null;
            $postType    = in_array($row['E'] ?? 'post', ['post', 'achievement', 'project', 'assignment'])
                            ? ($row['E'] ?? 'post')
                            : 'post';

            if (!$title && !$content) {
                continue;
            }

            Post::create([
                'user_id'      => $this->userId,
                'page_id'      => $this->pageId,
                'title'        => $title,
                'content'      => $content,
                'visibility'   => $visibility,
                'post_type'    => $postType,
                'scheduled_at' => $scheduled,
            ]);
        }

        // Clean up
        Storage::disk('local')->delete($this->filePath);
    }
}