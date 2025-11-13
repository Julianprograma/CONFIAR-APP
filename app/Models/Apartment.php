<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Apartment extends Model
{
    use HasFactory;

    protected $fillable = [
        // Add common fields for apartments as needed
        'code',
        'owner_id',
    ];

    public function dues()
    {
        return $this->hasMany(MonthlyDue::class);
    }
}
