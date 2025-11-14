<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Apartment extends Model
{
    use HasFactory;

    protected $fillable = [
        'apartment_number',
        'owner_id',
        'square_meters',
    ];

    /**
     * Relación: Un apartamento tiene un propietario (User).
     */
    public function owner()
    {
        // La clave foránea es 'owner_id' en la tabla apartments
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Relación: Un apartamento tiene muchas cuotas mensuales (MonthlyDues).
     */
    public function monthlyDues()
    {
        return $this->hasMany(MonthlyDue::class, 'apartment_id');
    }
}