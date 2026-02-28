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
        if (! Schema::hasTable('shipment_api_key')) {
            Schema::create('shipment_api_key', function (Blueprint $table) {
                $table->id();
                $table->string('provider', 50)->default('shiprocket')->unique();
                $table->string('api_email', 150);
                $table->text('api_password');
                $table->text('api_token')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipment_api_key');
    }
};
