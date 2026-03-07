<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('task_proofs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_task_id');
            $table->string('file_url');
            $table->string('latitude')->nullable(); // Lưu vĩ độ
            $table->string('longitude')->nullable(); // Lưu kinh độ
            $table->integer('version')->default(1); // Lưu bản 1, bản 2...
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('task_proofs');
    }
};