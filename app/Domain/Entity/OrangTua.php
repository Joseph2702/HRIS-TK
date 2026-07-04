<?php

namespace App\Domain\Entity;

use Illuminate\Database\Eloquent\Model;

class OrangTua extends Model
{
    protected $table = 'orang_tua';
    protected $primaryKey = 'id_orang_tua';
    public $timestamps = false;

    protected $fillable = ['id_user', 'pekerjaan'];

    protected function casts(): array
    {
        return ['created_at' => 'datetime'];
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function murid()
    {
        return $this->hasMany(Murid::class, 'id_orang_tua', 'id_orang_tua');
    }
}
