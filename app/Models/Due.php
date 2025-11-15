<?php
// filepath: c:\Users\pipem\Documents\OCTAVO_SEMESTRE\COMUNICACIÓN_DE_DATOS\CONFIAR-APP\app\Models\Due.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Due extends Model
{
    protected $fillable = [
        'apartment_id',
        'period',
        'concept',
        'amount',
        'due_date',
        'payment_date',
        'status',
    ];

    protected $casts = [
        'due_date' => 'date',
        'payment_date' => 'date',
        'period' => 'date',
    ];

    public function apartment()
    {
        return $this->belongsTo(Apartment::class);
    }
}