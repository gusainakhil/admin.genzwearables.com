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
        if (! Schema::hasTable('company_details')) {
            Schema::create('company_details', function (Blueprint $table) {
                $table->id();
                $table->string('brand_name', 150);
                $table->string('logo')->nullable();
                $table->string('favicon')->nullable();
                $table->text('address')->nullable();
                $table->string('gst_number', 30)->nullable()->unique();
                $table->string('phone_number1', 20)->nullable();
                $table->string('phone_number2', 20)->nullable();
                $table->string('website_name', 150)->nullable();
                $table->string('support_email', 150)->nullable();
                $table->text('additional_info')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_details');
    }
};
