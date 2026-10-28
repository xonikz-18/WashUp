<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /*
         * Services need an active/inactive state
         * so staff can disable a service without
         * deleting historical records.
         */
        Schema::table('services', function (Blueprint $table) {
            $table->boolean('is_active')
                ->default(true);
        });

        /*
         * Add-ons also need an active/inactive state.
         */
        Schema::table('addons', function (Blueprint $table) {
            $table->boolean('is_active')
                ->default(true);
        });

        /*
         * Preserve the service price used when
         * each order was created.
         *
         * This prevents old orders from changing
         * when staff edits the current service price.
         */
        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('service_price_per_kg', 10, 2)
                ->nullable()
                ->after('service_id');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('service_price_per_kg');
        });

        Schema::table('addons', function (Blueprint $table) {
            $table->dropColumn('is_active');
        });

        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn('is_active');
        });
    }
};