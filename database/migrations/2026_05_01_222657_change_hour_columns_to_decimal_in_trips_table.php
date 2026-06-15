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
            $table->decimal('open_hour', 12, 2)->nullable()->change();
            $table->decimal('close_hour', 12, 2)->nullable()->change();
            $table->decimal('total_hour', 12, 2)->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trips', function (Blueprint $table) {
            $table->time('open_hour')->nullable()->change();
            $table->time('close_hour')->nullable()->change();
            $table->integer('total_hour')->default(0)->change();
        });
    }

};
