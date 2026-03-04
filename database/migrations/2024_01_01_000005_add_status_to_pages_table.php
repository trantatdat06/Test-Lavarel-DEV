<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->enum('status', ['pending', 'approved', 'rejected'])
                  ->default('pending')->after('type');
            $table->enum('category', [
                'student_union', 'youth_union', 'club', 'faculty', 'department', 'other'
            ])->default('other')->after('status');
            $table->text('reject_reason')->nullable()->after('category');
            $table->timestamp('approved_at')->nullable()->after('reject_reason');
            $table->unsignedBigInteger('approved_by')->nullable()->after('approved_at');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
        });

        // Auto-approve trang của super_admin
        DB::statement("
            UPDATE pages SET status = 'approved', approved_at = created_at
            WHERE created_by IN (SELECT id FROM users WHERE role = 'super_admin')
        ");
    }

    public function down(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
            $table->dropColumn(['status','category','reject_reason','approved_at','approved_by']);
        });
    }
};