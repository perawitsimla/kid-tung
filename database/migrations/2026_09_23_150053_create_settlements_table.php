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
    Schema::create('settlements', function (Blueprint $table) {
        $table->char('stl_id', 8)->primary();
        $table->char('stl_debtor_id', 8);
        $table->char('stl_creditor_id', 8);
        $table->decimal('stl_amount', 10, 2);
        $table->string('stl_slip_image', 255)->nullable();
        $table->string('stl_status', 45)->default('pending');
        $table->timestamp('stl_createdAt')->useCurrent();
        $table->timestamp('stl_updatedAt')->useCurrent();
        $table->softDeletes('stl_deletedAt');

        $table->foreign('stl_debtor_id')->references('user_id')->on('users')->onDelete('cascade');
        $table->foreign('stl_creditor_id')->references('user_id')->on('users')->onDelete('cascade');
    });
}
public function down(): void { Schema::dropIfExists('settlements'); }
};
