<?php

namespace App\Domain\Entity;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table = 'roles';
    protected $primaryKey = 'id_role';
    public $timestamps = false;

    protected $fillable = ['nama_role', 'is_active'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'created_at' => 'datetime',
        ];
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permissions', 'id_role', 'id_permission');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_roles', 'id_role', 'id_user')
            ->withPivot('is_active');
    }
}
