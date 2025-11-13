<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonthlyDue extends Model
{
    use HasFactory;

    protected $fillable = [
        'apartment_id',
        'period_id',
        'base_amount',
        'due_date',
        'status',
        'payment_date',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'due_date' => 'datetime',
        'payment_date' => 'datetime',
    ];

    public function apartment()
    {
        return $this->belongsTo(Apartment::class);
    }

    public function period()
    {
        return $this->belongsTo(BillingPeriod::class, 'period_id');
    }
}
