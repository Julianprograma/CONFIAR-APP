<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Basado en el esquema SQL: monthly_dues (id BIGINT, apartment_id INT, period_id INT, ...)
     */
    public function up(): void
    {
        Schema::create('monthly_dues', function (Blueprint $table) {
            $table->id(); // Crea un BIGINT ID
            
            // Tus migraciones de 'apartments' y 'billing_periods' usan increments('id') (INT)
            $table->unsignedInteger('apartment_id');
            $table->unsignedInteger('period_id');
            
            $table->decimal('base_amount', 15, 2);
            $table->date('due_date');
            $table->date('payment_date')->nullable();
            $table->enum('status', ['Pendiente', 'Pagada', 'Vencida']);
            $table->timestamps();

            // Claves foráneas
            $table->foreign('apartment_id')->references('id')->on('apartments');
            $table->foreign('period_id')->references('id')->on('billing_periods');

            // Restricción Única
            $table->unique(['apartment_id', 'period_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monthly_dues');
    }
};