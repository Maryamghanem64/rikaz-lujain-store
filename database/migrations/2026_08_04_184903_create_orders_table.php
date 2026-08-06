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
    Schema::create('orders', function (Blueprint $table) {
        $table->id();

        $table->string('order_number')->unique();

        $table->string('customer_name');
        $table->string('customer_phone');
        $table->string('customer_whatsapp')->nullable();

        $table->foreignId('delivery_zone_id')
            ->nullable()
            ->constrained()
            ->nullOnDelete();

        // Snapshot حتى تغيير أو حذف المنطقة لا يغيّر الطلب القديم.
        $table->string('delivery_zone_name');
        $table->text('address');
        $table->text('notes')->nullable();

        $table->decimal('subtotal', 10, 2);
        $table->decimal('delivery_fee', 10, 2);
        $table->decimal('total', 10, 2);

        $table->string('payment_method');
        $table->string('payment_status');
        $table->string('status');

        $table->timestamp('reservation_expires_at')->nullable();

        $table->timestamps();

        $table->index('customer_phone');
        $table->index('payment_status');
        $table->index('status');
        $table->index('reservation_expires_at');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
{
    Schema::dropIfExists('orders');
}
};
