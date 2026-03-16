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
        Schema::create('returns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_item_id')->constrained();
            $table->enum('request_type', ['return', 'replacement'])->default('return')->index();
            $table->foreignId('product_variant_id')->nullable()->constrained()->nullOnDelete();
            $table->json('product_images')->nullable();
            $table->text('reason')->nullable();
            $table->enum('status', ['requested', 'approved', 'rejected', 'refunded'])->default('requested')->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('returns');
    }
};
