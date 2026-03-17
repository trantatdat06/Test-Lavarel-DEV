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
    // Đổi enum của cột role trong users
    DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('super_admin', 'system_admin', 'user') NOT NULL DEFAULT 'user'");
}

public function down()
{
    DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('super_admin', 'user', 'page_admin') NOT NULL DEFAULT 'user'");
}
};
