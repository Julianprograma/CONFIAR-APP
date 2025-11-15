<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Basado en el esquema SQL: invoices_payable (id BIGINT, supplier_id INT, ...)
     */
    public function up(): void
    {
        Schema::create('invoices_payable', function (Blueprint $table) {
            $table->id(); // Crea un BIGINT ID
            $table->unsignedInteger('supplier_id')->nullable(); // Coincide con INT ID de suppliers
            $table->date('issue_date');
            $table->date('due_date');
            $table->decimal('amount', 15, 2);
            $table->string('support_file', 255)->nullable();
            $table->enum('status', ['Pendiente', 'Pagada', 'Anulada']);
            $table->timestamps();

            // Clave foránea
            $table->foreign('supplier_id')->references('id')->on('suppliers');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices_payable');
    }
};