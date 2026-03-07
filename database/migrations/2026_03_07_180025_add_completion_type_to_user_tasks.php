<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('user_tasks', function (Blueprint $table) {
            $table->string('completion_type')->default('simple')->after('description'); 
            // 'simple' = Nút Xong, 'proof' = Bắt buộc nộp ảnh + GPS
        });
    }
    public function down(): void {
        Schema::table('user_tasks', function (Blueprint $table) {
            $table->dropColumn('completion_type');
        });
    }
};