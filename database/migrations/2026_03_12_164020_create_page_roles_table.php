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
    Schema::create('page_roles', function (Blueprint $table) {
        $table->id();
        $table->foreignId('page_id')->constrained('pages')->onDelete('cascade');
        $table->string('name'); // Tên role do user tự đặt (VD: "Ban Truyền Thông", "Phó Nhóm")
        $table->json('permissions')->nullable(); // Lưu mảng quyền hạn. VD: ["create_post", "delete_comment", "approve_member"]
        $table->boolean('is_default')->default(false); // Đánh dấu role mặc định (vd: Chủ trang) không được xóa
        $table->timestamps();
    });
}

public function down()
{
    Schema::dropIfExists('page_roles');
}
};
