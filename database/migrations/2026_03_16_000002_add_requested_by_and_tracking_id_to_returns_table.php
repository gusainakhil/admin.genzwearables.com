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
            $table->enum('requested_by', ['self', 'user'])->default('user')->index()->after('request_type');
            $table->string('tracking_id', 100)->nullable()->index()->after('product_variant_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('returns', function (Blueprint $table) {
            $table->dropIndex('returns_requested_by_index');
            $table->dropIndex('returns_tracking_id_index');
            $table->dropColumn(['requested_by', 'tracking_id']);
        });
    }
};
