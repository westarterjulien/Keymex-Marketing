<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'microsoft_id',
        'keymex_id',
        'avatar',
        'role',
        'sso_groups',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'sso_groups' => 'array',
        ];
    }

    /**
     * Verifie si l'utilisateur a au moins le role specifie
     */
    public function hasRole(string $role): bool
    {
        return SsoGroupMapping::hasMinimumRole($this->role ?? 'viewer', $role);
    }

    /**
     * Verifie si l'utilisateur est super-admin
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === 'super-admin';
    }

    /**
     * Verifie si l'utilisateur est admin (ou super-admin)
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    /**
     * Verifie si l'utilisateur peut editer (editor, admin ou super-admin)
     */
    public function canEdit(): bool
    {
        return $this->hasRole('editor');
    }
}
