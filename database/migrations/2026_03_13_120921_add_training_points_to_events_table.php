<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->boolean('has_training_point')->default(false); // Có cấp điểm không?
            $table->integer('point_amount')->default(0); // Số điểm được cấp
            $table->string('point_category')->nullable(); // Tiêu chí (VD: Tình nguyện, Học thuật...)
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn(['has_training_point', 'point_amount', 'point_category']);
        });
    }
};