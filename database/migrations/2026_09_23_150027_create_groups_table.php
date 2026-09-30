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
    Schema::create('groups', function (Blueprint $table) {
        $table->char('group_id', 8)->primary();
        $table->string('group_name', 100);
        $table->timestamp('group_createdAt')->useCurrent();
        $table->timestamp('group_updatedAt')->useCurrent();
        $table->softDeletes('group_deletedAt');
    });
}
public function down(): void { Schema::dropIfExists('groups'); 
}
};
