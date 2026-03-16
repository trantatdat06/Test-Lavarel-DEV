<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Chạy migration để thêm cột.
     */
    public function up(): void
    {
        Schema::table('forms', function (Blueprint $table) {
            // Thêm cột post_id để liên kết với bài viết
            $table->unsignedBigInteger('post_id')->nullable()->after('event_id');
            
            // Thêm 2 cột thời gian đóng mở form
            $table->dateTime('start_time')->nullable()->after('description');
            $table->dateTime('end_time')->nullable()->after('start_time');
        });
    }

    /**
     * Hoàn tác migration (xóa cột nếu cần rollback).
     */
    public function down(): void
    {
        Schema::table('forms', function (Blueprint $table) {
            $table->dropColumn(['post_id', 'start_time', 'end_time']);
        });
    }
};