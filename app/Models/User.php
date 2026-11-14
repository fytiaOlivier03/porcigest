<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    public const ROLES = ['admin', 'responsable', 'agent'];

    // exploitation_id volontairement absent : il est affecté par le serveur, jamais par le formulaire.
    protected $fillable = ['name', 'email', 'password', 'role', 'actif'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'actif' => 'boolean',
        ];
    }

    public function exploitation()
    {
        return $this->belongsTo(Exploitation::class);
    }

    public function aRole(string ...$roles): bool
    {
        return in_array($this->role, $roles, true);
    }
}
