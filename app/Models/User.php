<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    // 1) Columna real del hash
    protected $passwordColumn = 'password_hash';
    protected $primaryKey = 'id';

    // 2) Timestamps (sin updated_at)
    public $timestamps = true;
    const UPDATED_AT = null;

    /**
     * Mass assignable
     */
    protected $fillable = [
        'role_id',
        'first_name',
        'last_name',
        'email',
        'password_hash',
        'phone_number',
        'is_active',
    ];

    /**
     * Hidden
     */
    protected $hidden = [
        'password_hash',
        'remember_token',
    ];

    // Autenticación: devolver el hash desde password_hash
    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    // Permitir asignar 'password' y guardarlo en password_hash (el valor ya debe venir hasheado)
    public function setPasswordAttribute($value)
    {
        $this->attributes['password_hash'] = $value;
    }

    /**
     * Relaciones
     */
    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function apartment()
    {
        return $this->hasOne(Apartment::class, 'owner_id');
    }
}