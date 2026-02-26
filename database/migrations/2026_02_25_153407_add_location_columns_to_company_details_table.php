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
        Schema::table('company_details', function (Blueprint $table) {
            $table->string('city', 100)->nullable()->after('address');
            $table->string('district', 100)->nullable()->after('city');
            $table->string('pincode', 20)->nullable()->after('district');
            $table->string('state', 100)->nullable()->after('pincode');
            $table->string('country', 100)->nullable()->after('state');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('company_details', function (Blueprint $table) {
            $table->dropColumn(['city', 'district', 'pincode', 'state', 'country']);
        });
    }
};
