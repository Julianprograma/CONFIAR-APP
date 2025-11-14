<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('apartments', function (Blueprint $table) {
            $table->increments('id'); // ID de Apartamento (INT UNSIGNED)
            $table->string('apartment_number', 10)->unique();
            
            // Relación con el propietario. Puede ser NULL si el apartamento no tiene un residente asignado.
            $table->foreignId('owner_id')
                  ->nullable()
                  ->constrained('users')
                  ->onDelete('set null'); 

            $table->decimal('square_meters', 10, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('apartments');
    }
};