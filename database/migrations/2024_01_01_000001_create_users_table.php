<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('student_code')->unique()->nullable();
            $table->string('email')->unique();
            $table->string('password');
            $table->string('full_name');
            $table->string('display_name')->nullable();
            $table->string('phone')->nullable();
            $table->unsignedBigInteger('faculty_id')->nullable();
            $table->unsignedBigInteger('class_id')->nullable();
            $table->enum('role', ['super_admin', 'user', 'page_admin'])->default('user');
            $table->unsignedInteger('upgrade_attempt_count')->default(0);
            $table->timestamp('upgrade_locked_at')->nullable();
            $table->boolean('first_login')->default(true);
            $table->string('avatar')->nullable();
            $table->string('bio')->nullable();
            $table->string('website')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};