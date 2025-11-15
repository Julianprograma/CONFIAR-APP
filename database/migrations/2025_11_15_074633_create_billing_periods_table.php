<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Basado en el esquema SQL: billing_periods (id INT, period_name VARCHAR, ...)
     */
    public function up(): void
    {
        Schema::create('billing_periods', function (Blueprint $table) {
            $table->increments('id'); // Crea un INT ID
            $table->string('period_name', 50)->unique();
            $table->date('start_date');
            $table->date('end_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('billing_periods');
    }
};