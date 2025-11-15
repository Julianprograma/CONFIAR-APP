<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Apartment extends Model
{
    use HasFactory;

    protected $fillable = [
        'owner_id',
        'number',
        'tower',
        'area',
        'coefficient'
    ];

    /**
     * Relación: Un apartamento tiene un propietario (User).
     */
    public function owner()
    {
        // La clave foránea es 'owner_id' en la tabla apartments
        return $this->belongsTo(User::class, 'owner_id');
    }
}