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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();

            $table->string('store_name_ar')
                ->default('ركاز × لجين');

            $table->string('phone')->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('instagram_url')->nullable();
            $table->string('whish_number')->nullable();

            $table->string('currency', 3)
                ->default('USD');

            $table->unsignedSmallInteger('reservation_hours')
                ->default(24);

            $table->text('about_text_ar')->nullable();
            $table->text('policy_text_ar')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
