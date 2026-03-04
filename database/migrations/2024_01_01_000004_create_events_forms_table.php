<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Faculties
        Schema::create('faculties', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->nullable()->unique();
            $table->timestamps();
        });

        // Events
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('page_id')->nullable();
            $table->unsignedBigInteger('post_id')->nullable();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('location')->nullable();
            $table->timestamp('start_time');
            $table->timestamp('end_time');
            $table->timestamp('form_open_at')->nullable();
            $table->timestamp('form_close_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('page_id')->references('id')->on('pages')->onDelete('set null');
            $table->foreign('post_id')->references('id')->on('posts')->onDelete('set null');
        });

        // Event participants (user calendar)
        Schema::create('event_participants', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('event_id');
            $table->unsignedBigInteger('user_id');
            $table->enum('status', ['going', 'interested', 'not_going'])->default('going');
            $table->timestamps();

            $table->unique(['event_id', 'user_id']);
            $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        // Dynamic Forms
        Schema::create('forms', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('event_id')->nullable();
            $table->string('title');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('event_id')->references('id')->on('events')->onDelete('set null');
        });

        Schema::create('form_fields', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('form_id');
            $table->string('label');
            $table->enum('type', ['text', 'textarea', 'select', 'radio', 'checkbox', 'date', 'file', 'email', 'phone']);
            $table->string('mapping_key')->nullable(); // student_code, full_name, email, etc.
            $table->json('options')->nullable(); // for select/radio/checkbox
            $table->boolean('required')->default(false);
            $table->unsignedInteger('order')->default(0);
            $table->timestamps();

            $table->foreign('form_id')->references('id')->on('forms')->onDelete('cascade');
        });

        // Form Submissions
        Schema::create('form_submissions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('form_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->json('data'); // { field_id: value }
            $table->enum('status', ['pending', 'approved', 'rejected', 'cancelled'])->default('pending');
            $table->timestamp('submitted_at')->useCurrent();
            $table->text('note')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('form_id')->references('id')->on('forms')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });

        // Notifications
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->unsignedBigInteger('user_id')->index();
            $table->string('type');
            $table->json('data');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('form_submissions');
        Schema::dropIfExists('form_fields');
        Schema::dropIfExists('forms');
        Schema::dropIfExists('event_participants');
        Schema::dropIfExists('events');
    }
};