<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    // 1) Columna real del hash (opcional pero claro para MySQL)
    protected $passwordColumn = 'password_hash';
    protected $primaryKey = 'id';

    // 2) Timestamps (sin updated_at si no existe en tu BD)
    public $timestamps = true;
    const UPDATED_AT = null;

    /**
     * Mass assignable
     * Incluye password_hash para permitir asignación masiva en User::create()
     */
    protected $fillable = [
        'role_id',
        'first_name',
        'last_name',
        'email',
        'password', // <--- CAMBIAR 'password_hash' a 'password' o añadir 'password'
        'password_hash', // <--- MANTENER ESTO SI QUIERES, PERO AÑADIR 'password'
        'phone_number',
        'is_active',
    ];

    /**
     * Hidden
     * Oculta password_hash en JSON para seguridad
     */
    protected $hidden = [
        'password_hash', // CORRECTO: No exponer el hash en respuestas API
        'remember_token',
    ];

    /**
     * Autenticación: devolver el hash desde password_hash
     * CLAVE para que Auth::attempt funcione con tu columna personalizada
     */
    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    /**
     * Permitir asignar 'password' y guardarlo en password_hash
     * NOTA: El valor debe venir ya hasheado (Hash::make) desde el controlador
     * Ejemplo: $user->password = Hash::make('secret'); guardará en password_hash
     */
    public function setPasswordAttribute($value)
    {
        // Asumiendo que $value ya viene hasheado si se usa directamente
        // En AuthController usas User::create con 'password_hash' => Hash::make($data['password'])
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