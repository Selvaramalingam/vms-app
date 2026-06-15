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
        Schema::table('trips', function (Blueprint $table) {
            $table->index('vehicle_id');
            $table->index('driver_id');
            $table->index('date');
        });

        Schema::table('fuel_entries', function (Blueprint $table) {
            $table->index('vehicle_id');
            $table->index('trip_id');
        });

        Schema::table('maintenances', function (Blueprint $table) {
            $table->index('vehicle_id');
            $table->index('date');
        });

        Schema::table('trip_payments', function (Blueprint $table) {
            $table->index('trip_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trips', function (Blueprint $table) {
            $table->dropIndex(['vehicle_id']);
            $table->dropIndex(['driver_id']);
            $table->dropIndex(['date']);
        });

        Schema::table('fuel_entries', function (Blueprint $table) {
            $table->dropIndex(['vehicle_id']);
            $table->dropIndex(['trip_id']);
        });

        Schema::table('maintenances', function (Blueprint $table) {
            $table->dropIndex(['vehicle_id']);
            $table->dropIndex(['date']);
        });

        Schema::table('trip_payments', function (Blueprint $table) {
            $table->dropIndex(['trip_id']);
        });
    }
};
