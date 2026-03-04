<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('page_id')->nullable()->index();
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('parent_post_id')->nullable()->index(); // repost
            $table->string('title')->nullable();
            $table->longText('content')->nullable();
            $table->string('media_path')->nullable();
            $table->string('external_link')->nullable();
            $table->enum('visibility', ['public', 'private'])->default('public');
            $table->json('tags')->nullable();
            $table->enum('post_type', ['post', 'achievement', 'project', 'assignment'])->default('post');
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('page_id')->references('id')->on('pages')->onDelete('set null');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('parent_post_id')->references('id')->on('posts')->onDelete('set null');
        });

        Schema::create('post_likes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('post_id');
            $table->unsignedBigInteger('user_id');
            $table->timestamps();

            $table->unique(['post_id', 'user_id']);
            $table->foreign('post_id')->references('id')->on('posts')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('post_comments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('post_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->text('content');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('post_id')->references('id')->on('posts')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('parent_id')->references('id')->on('post_comments')->onDelete('set null');
        });

        Schema::create('post_saves', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('post_id');
            $table->unsignedBigInteger('user_id');
            $table->timestamps();

            $table->unique(['post_id', 'user_id']);
            $table->foreign('post_id')->references('id')->on('posts')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('post_saves');
        Schema::dropIfExists('post_comments');
        Schema::dropIfExists('post_likes');
        Schema::dropIfExists('posts');
    }
};