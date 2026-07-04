<?php

namespace App\Domain\Entity;

use Illuminate\Database\Eloquent\Model;

class Guru extends Model
{
    protected $table = 'guru';
    protected $primaryKey = 'id_guru';
    public $timestamps = false;

    protected $fillable = ['id_user', 'spesialisasi'];

    protected function casts(): array
    {
        return ['created_at' => 'datetime'];
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function jadwalKelas()
    {
        return $this->hasMany(JadwalKelas::class, 'id_guru', 'id_guru');
    }

    public function laporanKegiatan()
    {
        return $this->hasMany(LaporanKegiatan::class, 'id_guru', 'id_guru');
    }
}
