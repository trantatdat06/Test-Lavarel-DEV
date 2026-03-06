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
        $table->string('gender')->nullable()->after('phone');
        $table->date('dob')->nullable()->after('gender');
        // Cột JSON để lưu trạng thái bảo mật của từng trường (public, friends, private)
        $table->json('privacy_settings')->nullable()->after('website');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
