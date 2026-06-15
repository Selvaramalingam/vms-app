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
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('vehicle_number')->unique();
            $table->string('vehicle_type');
            $table->enum('owner_type', ['own', 'rent']);
            $table->string('owner_name')->nullable();
            $table->string('owner_phone')->nullable();
            $table->string('status')->default('active');
            
            // Expiry Tracking
            $table->date('fc_expiry')->nullable();
            $table->date('insurance_expiry')->nullable();
            $table->date('permit_expiry')->nullable();
            $table->date('tax_expiry')->nullable();
            $table->date('pollution_expiry')->nullable();
            
            // Service Tracking
            $table->integer('last_service_km')->default(0);
            $table->integer('next_service_km')->nullable();
            $table->date('last_service_date')->nullable();
            $table->date('next_service_date')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
