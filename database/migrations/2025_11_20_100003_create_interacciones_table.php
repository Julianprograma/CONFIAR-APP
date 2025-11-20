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
        Schema::create('interacciones', function (Blueprint $table) {
            $table->id();

            $table->foreignId('cuenta_cobro_id')->constrained('cuentas_cobro')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');

            $table->enum('tipo', [
                'nota_manual',
                'recordatorio_pago',
                'llamada',
                'email_enviado',
                'aprobacion',
                'rechazo',
                'devolucion',
                'pago_registrado'
            ])->default('nota_manual');

            $table->string('asunto', 200);
            $table->text('detalle')->nullable();
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
        Schema::dropIfExists('interacciones');
    }
};
