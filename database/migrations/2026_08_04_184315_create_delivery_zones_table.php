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
    Schema::create('delivery_zones', function (Blueprint $table) {
        $table->id();

        $table->string('name_ar');
        $table->decimal('fee', 8, 2);

        $table->boolean('is_active')->default(true);
        $table->unsignedSmallInteger('sort_order')->default(0);

        $table->timestamps();

        $table->index(['is_active', 'sort_order']);
    });
}
    /**
     * Reverse the migrations.
     */
   public function down(): void
{
    Schema::dropIfExists('delivery_zones');
}
};
