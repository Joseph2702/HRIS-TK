<?php

namespace App\Domain\Entity;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $table = 'permissions';
    protected $primaryKey = 'id_permission';
    public $timestamps = false;

    protected $fillable = ['nama_permission', 'deskripsi'];

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_permissions', 'id_permission', 'id_role');
    }
}
