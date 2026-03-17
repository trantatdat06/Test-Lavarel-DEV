<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('page_members', function (Blueprint $table) {
        // Xóa cột role cũ (dạng enum)
        $table->dropColumn('role');
        
        // Thêm cột page_role_id liên kết với bảng page_roles vừa tạo
        $table->foreignId('page_role_id')->nullable()->constrained('page_roles')->onDelete('set null');
    });
}

public function down()
{
    Schema::table('page_members', function (Blueprint $table) {
        $table->dropForeign(['page_role_id']);
        $table->dropColumn('page_role_id');
        $table->enum('role', ['admin', 'content_manager', 'member_manager', 'info_manager', 'system_manager'])->default('admin');
    });
}
};
