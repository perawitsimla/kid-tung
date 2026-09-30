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
    Schema::create('item_splits', function (Blueprint $table) {
        $table->char('is_id', 8)->primary();
        $table->char('is_item_id', 8);
        $table->char('is_user_id', 8);
        $table->decimal('is_amount', 10, 2);
        $table->string('is_split_method', 45)->default('equal');
        $table->timestamp('is_createdAt')->useCurrent();
        $table->timestamp('is_updatedAt')->useCurrent();
        $table->softDeletes('is_deletedAt');

        $table->foreign('is_item_id')->references('item_id')->on('expense_items')->onDelete('cascade');
        $table->foreign('is_user_id')->references('user_id')->on('users')->onDelete('cascade');
    });
}
public function down(): void { Schema::dropIfExists('item_splits'); }
};
