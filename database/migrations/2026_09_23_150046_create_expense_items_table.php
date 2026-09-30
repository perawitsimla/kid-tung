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
    Schema::create('expense_items', function (Blueprint $table) {
        $table->char('item_id', 8)->primary();
        $table->char('item_exp_id', 8);
        $table->char('item_payer_id', 8);
        $table->string('item_name', 150);
        $table->decimal('item_price', 10, 2);
        $table->timestamp('item_createdAt')->useCurrent();
        $table->timestamp('item_updatedAt')->useCurrent();
        $table->softDeletes('item_deletedAt');

        $table->foreign('item_exp_id')->references('exp_id')->on('expenses')->onDelete('cascade');
        $table->foreign('item_payer_id')->references('user_id')->on('users')->onDelete('cascade');
    });
}
public function down(): void { Schema::dropIfExists('expense_items'); }
};
