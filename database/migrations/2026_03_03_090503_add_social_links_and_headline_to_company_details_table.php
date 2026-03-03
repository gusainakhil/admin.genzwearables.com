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
            $table->string('company_headline', 255)->nullable()->after('brand_name');
            $table->string('youtube_url', 255)->nullable()->after('additional_info');
            $table->string('facebook_url', 255)->nullable()->after('youtube_url');
            $table->string('pinterest_url', 255)->nullable()->after('facebook_url');
            $table->string('twitter_url', 255)->nullable()->after('pinterest_url');
            $table->string('linkedin_url', 255)->nullable()->after('twitter_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('company_details', function (Blueprint $table) {
            $table->dropColumn([
                'company_headline',
                'youtube_url',
                'facebook_url',
                'pinterest_url',
                'twitter_url',
                'linkedin_url',
            ]);
        });
    }
};
