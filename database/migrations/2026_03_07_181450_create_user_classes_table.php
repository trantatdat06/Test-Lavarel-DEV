<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_classes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('title'); // Tên môn học
            $table->integer('day_of_week'); // Lưu số: 1=CN, 2=Thứ 2... 7=Thứ 7
            $table->time('start_time'); // Giờ bắt đầu
            $table->time('end_time')->nullable(); // Giờ kết thúc
            $table->string('location')->nullable(); // Phòng học
            $table->string('color')->default('#23a559'); // Màu xanh lá mặc định
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_classes');
    }
};
