<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('event_participants', function (Blueprint $table) {
            $table->string('proof_file')->nullable()->after('status'); // Lưu link file minh chứng
            $table->text('proof_note')->nullable()->after('proof_file'); // Lưu ghi chú
            $table->enum('proof_status', ['none', 'pending', 'approved', 'rejected'])->default('none')->after('proof_note'); // Trạng thái duyệt
        });
    }

    public function down()
    {
        Schema::table('event_participants', function (Blueprint $table) {
            $table->dropColumn(['proof_file', 'proof_note', 'proof_status']);
        });
    }
};