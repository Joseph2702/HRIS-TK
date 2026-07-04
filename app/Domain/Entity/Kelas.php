<?php

namespace App\Domain\Entity;

use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    protected $table = 'kelas';
    protected $primaryKey = 'id_kelas';
    public $timestamps = false;

    protected $fillable = ['nama_kelas', 'deskripsi', 'kapasitas'];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function murid()
    {
        return $this->hasMany(Murid::class, 'id_kelas', 'id_kelas');
    }

    public function jadwalKelas()
    {
        return $this->hasMany(JadwalKelas::class, 'id_kelas', 'id_kelas');
    }
}
