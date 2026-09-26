<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->char('user_id', 8)->primary();
            $table->string('user_name', 100);
            $table->string('user_email', 100)->unique();
            $table->string('user_password', 255);
            $table->string('user_promptpay_no', 20)->nullable();
            $table->timestamp('user_createdAt')->useCurrent();
            $table->timestamp('user_updatedAt')->useCurrent()->useCurrentOnUpdate();
            $table->timestamp('user_deletedAt')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};