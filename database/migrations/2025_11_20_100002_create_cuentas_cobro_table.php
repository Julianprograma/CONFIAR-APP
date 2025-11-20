<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cuentas_cobro', function (Blueprint $table) {
            $table->id();
            
            // TODO: Add foreign key constraint once 'contratos' table is defined.
            $table->unsignedBigInteger('contrato_id'); 
            
            $table->date('fecha_emision');
            $table->date('fecha_vencimiento');
            $table->decimal('monto', 15, 2);
            $table->string('estado_actual')->default('Borrador');
            $table->text('observaciones')->nullable();
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
        Schema::dropIfExists('cuentas_cobro');
    }
};
