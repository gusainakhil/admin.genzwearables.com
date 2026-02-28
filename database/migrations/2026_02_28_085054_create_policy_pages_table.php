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
        if (! Schema::hasTable('policy_pages')) {
            Schema::create('policy_pages', function (Blueprint $table) {
                $table->id();
                $table->longText('privacy_policy')->nullable();
                $table->longText('terms_and_conditions')->nullable();
                $table->longText('return_and_refund')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('policy_pages');
    }
};
