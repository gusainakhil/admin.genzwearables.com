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
        if (Schema::hasTable('products')) {
            Schema::table('products', function (Blueprint $table) {
                if (! Schema::hasColumn('products', 'meta_title')) {
                    $table->string('meta_title', 255)->nullable()->after('status');
                }

                if (! Schema::hasColumn('products', 'meta_description')) {
                    $table->text('meta_description')->nullable()->after('meta_title');
                }

                if (! Schema::hasColumn('products', 'og_title')) {
                    $table->string('og_title', 255)->nullable()->after('meta_description');
                }

                if (! Schema::hasColumn('products', 'og_description')) {
                    $table->text('og_description')->nullable()->after('og_title');
                }

                if (! Schema::hasColumn('products', 'og_image')) {
                    $table->string('og_image', 500)->nullable()->after('og_description');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('products')) {
            Schema::table('products', function (Blueprint $table) {
                $columns = collect(['meta_title', 'meta_description', 'og_title', 'og_description', 'og_image'])
                    ->filter(fn (string $column): bool => Schema::hasColumn('products', $column))
                    ->values()
                    ->all();

                if ($columns !== []) {
                    $table->dropColumn($columns);
                }
            });
        }
    }
};
