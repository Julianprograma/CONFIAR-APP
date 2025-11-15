<?php
// filepath: c:\Users\pipem\Documents\OCTAVO_SEMESTRE\COMUNICACIÓN_DE_DATOS\CONFIAR-APP\app\Models\InvoicePayable.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoicePayable extends Model
{
    protected $fillable = [
        'supplier_name',
        'invoice_number',
        'amount',
        'issue_date',
        'due_date',
        'status',
        'payment_date',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'due_date' => 'date',
        'payment_date' => 'date',
    ];
}