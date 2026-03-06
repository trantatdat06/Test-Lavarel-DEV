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
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn('class_id'); // Xóa class_id cũ
        $table->string('class_name')->nullable()->after('faculty_id'); // Thêm class_name tự do nhập
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
