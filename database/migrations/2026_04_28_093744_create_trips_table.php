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
        Schema::create('trips', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('location');
            $table->foreignId('vehicle_id')->constrained()->cascadeOnDelete();
            $table->foreignId('driver_id')->constrained()->cascadeOnDelete();
            $table->enum('trip_type', ['rent', 'own', 'empty']);
            
            // KM
            $table->integer('open_km')->default(0);
            $table->integer('close_km')->default(0);
            $table->integer('total_km')->default(0);
            
            // Hour
            $table->time('open_hour')->nullable();
            $table->time('close_hour')->nullable();
            $table->integer('total_hour')->default(0);
            
            // Money
            $table->decimal('rent_amount', 10, 2)->default(0);
            $table->decimal('diesel_price', 8, 2)->default(0);
            $table->decimal('fuel_litre', 8, 2)->default(0);
            $table->decimal('fuel_cost', 10, 2)->default(0);
            
            // Notes
            $table->text('maintenance_note')->nullable();
            $table->text('loan_note')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trips');
    }
};
