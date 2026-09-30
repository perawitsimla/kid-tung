<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void {
    Schema::create('group_members', function (Blueprint $table) {
        $table->char('gm_id', 8)->primary();
        $table->char('gm_group_id', 8);
        $table->char('gm_user_id', 8);
        $table->string('gm_role', 45)->default('member');
        $table->timestamp('gm_createdAt')->useCurrent();
        $table->timestamp('gm_updatedAt')->useCurrent();
        $table->softDeletes('gm_deletedAt');

        $table->foreign('gm_group_id')->references('group_id')->on('groups')->onDelete('cascade');
        $table->foreign('gm_user_id')->references('user_id')->on('users')->onDelete('cascade');
    });
}
public function down(): void { Schema::dropIfExists('group_members'); 
}
};
