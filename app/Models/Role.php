<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    // La tabla por defecto es 'roles'
    protected $fillable = ['name'];
    public const SUPER_USUARIO = 1;
    public const ADMINISTRADOR = 2;
    public const RESIDENTE = 3;
    public function users()
    {
        return $this->hasMany(User::class);
    }
}