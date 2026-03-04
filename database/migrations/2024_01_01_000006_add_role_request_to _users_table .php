<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Trạng thái tài khoản
            $table->enum('account_status', ['active', 'pending_upgrade', 'suspended'])
                  ->default('active')->after('role');

            // Yêu cầu nâng cấp lên page_admin
            $table->enum('requested_role', [
                'student_union',  // Hội Sinh viên
                'youth_union',    // Đoàn Thanh niên
                'club_admin',     // Ban quản trị CLB
                'faculty_staff',  // Cán bộ Khoa
                'department_staff', // Nhân viên phòng ban
            ])->nullable()->after('account_status');

            $table->text('role_request_reason')->nullable()->after('requested_role');
            $table->string('role_request_evidence')->nullable()->after('role_request_reason'); // file đính kèm
            $table->timestamp('role_requested_at')->nullable()->after('role_request_evidence');
            $table->timestamp('role_approved_at')->nullable()->after('role_requested_at');
            $table->unsignedBigInteger('role_approved_by')->nullable()->after('role_approved_at');
            $table->text('role_reject_reason')->nullable()->after('role_approved_by');

            $table->foreign('role_approved_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_approved_by']);
            $table->dropColumn([
                'account_status', 'requested_role', 'role_request_reason',
                'role_request_evidence', 'role_requested_at', 'role_approved_at',
                'role_approved_by', 'role_reject_reason',
            ]);
        });
    }
};