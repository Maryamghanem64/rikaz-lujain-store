<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up(): void
{
    Schema::create('order_items', function (Blueprint $table) {
        $table->id();

        $table->foreignId('order_id')
            ->constrained()
            ->cascadeOnDelete();

        $table->foreignId('product_id')
            ->nullable()
            ->constrained()
            ->nullOnDelete();

        // Product snapshots
        $table->string('product_name_ar');
        $table->string('stone_name')->nullable();
        $table->decimal('stone_weight', 8, 2)->nullable();
        $table->string('silver_purity');
        $table->string('size')->nullable();

        $table->decimal('unit_price', 10, 2);
        $table->unsignedInteger('quantity');
        $table->decimal('subtotal', 10, 2);

        $table->timestamps();

        $table->index('order_id');
        $table->index('product_id');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
{
    Schema::dropIfExists('order_items');
}
};
