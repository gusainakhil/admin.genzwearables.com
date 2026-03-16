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
        Schema::table('returns', function (Blueprint $table) {
            $table->enum('request_type', ['return', 'replacement'])->default('return')->index()->after('order_item_id');
            $table->foreignId('product_variant_id')->nullable()->after('request_type')->constrained()->nullOnDelete();
            $table->json('product_images')->nullable()->after('product_variant_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('returns', function (Blueprint $table) {
            $table->dropIndex('returns_request_type_index');
            $table->dropForeign(['product_variant_id']);
            $table->dropColumn(['request_type', 'product_variant_id', 'product_images']);
        });
    }
};
