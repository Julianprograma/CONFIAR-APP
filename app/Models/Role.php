<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    // La tabla por defecto es 'roles'
    protected $fillable = ['name'];

    /**
     * Define la relación de uno a muchos con los usuarios (users.role_id).
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }
}