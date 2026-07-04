<?php

namespace App\Domain\Entity;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    protected $table = 'users';
    protected $primaryKey = 'id_user';
    public $timestamps = false;

    protected $fillable = [
        'nama', 'email', 'password', 'no_hp', 'jenis_kelamin',
        'tempat_lahir', 'tanggal_lahir', 'foto_profile', 'status',
    ];

    protected $hidden = ['password'];

    protected function casts(): array
    {
        return [
            'tanggal_lahir' => 'date',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getJWTIdentifier(): mixed
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims(): array
    {
        return [];
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_roles', 'id_user', 'id_role')
            ->withPivot('is_active');
    }

    public function guru()
    {
        return $this->hasOne(Guru::class, 'id_user', 'id_user');
    }

    public function orangTua()
    {
        return $this->hasOne(OrangTua::class, 'id_user', 'id_user');
    }

    public function hasRole(string $roleName): bool
    {
        return $this->roles()
            ->where('roles.nama_role', $roleName)
            ->where('user_roles.is_active', true)
            ->exists();
    }

    public function getRoleNames(): array
    {
        return $this->roles()
            ->where('user_roles.is_active', true)
            ->pluck('roles.nama_role')
            ->toArray();
    }
}
