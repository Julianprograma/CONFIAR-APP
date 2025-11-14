<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            // 1. CLAVE FORÁNEA: Debe existir la tabla 'roles' antes de esta.
            $table->foreignId('role_id')
                  ->constrained('roles') // Hace referencia a la tabla 'roles'
                  ->onDelete('restrict'); // No permite borrar un rol si tiene usuarios

            // 2. CAMPOS DE NOMBRE
            $table->string('first_name', 100);
            $table->string('last_name', 100);

            $table->string('email', 100)->unique();
            $table->string('phone_number', 20)->nullable();
            $table->boolean('is_active')->default(true); // Booleano para activo/inactivo

            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
};
