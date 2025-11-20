<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $with = ['role'];
    protected $primaryKey = 'id';

    public $timestamps = true;

    /**
     * Mass assignable
     */
    protected $fillable = [
        'role_id',
        'name',
        'email',
        'password',
        'phone_number',
        'is_active',
    ];

    /**
     * Hidden
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Return the password for authentication. Support legacy 'password_hash' as fallback.
     */
    public function getAuthPassword()
    {
        return $this->password ?? $this->password_hash ?? '';
    }

    /**
     * If assigning password in plaintext, hash it automatically.
     */
    public function setPasswordAttribute($value)
    {
        if (empty($value)) {
            return;
        }

        // If value already looks like a bcrypt hash, keep it
        if (is_string($value) && str_starts_with($value, '$2y$')) {
            $this->attributes['password'] = $value;
            return;
        }

        $this->attributes['password'] = \Illuminate\Support\Facades\Hash::make($value);
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