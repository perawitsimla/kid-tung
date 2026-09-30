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
    Schema::create('expenses', function (Blueprint $table) {
        $table->char('exp_id', 8)->primary();
        $table->char('exp_group_id', 8);
        $table->string('exp_title', 150)->nullable();
        $table->decimal('exp_vat_percent', 5, 2)->default(0.00);
        $table->decimal('exp_sc_percent', 5, 2)->default(0.00);
        $table->timestamp('exp_createdAt')->useCurrent();
        $table->timestamp('exp_updatedAt')->useCurrent();
        $table->softDeletes('exp_deletedAt');

        $table->foreign('exp_group_id')->references('group_id')->on('groups')->onDelete('cascade');
    });
}
public function down(): void { Schema::dropIfExists('expenses'); }
};
