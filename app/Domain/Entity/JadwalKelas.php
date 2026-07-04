<?php

namespace App\Domain\Entity;

use Illuminate\Database\Eloquent\Model;

class JadwalKelas extends Model
{
    protected $table = 'jadwal_kelas';
    protected $primaryKey = 'id_jadwal';
    public $timestamps = false;

    protected $fillable = ['id_kelas', 'id_guru', 'tanggal', 'jam_mulai', 'jam_selesai', 'topik'];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'id_kelas', 'id_kelas');
    }

    public function guru()
    {
        return $this->belongsTo(Guru::class, 'id_guru', 'id_guru');
    }

    public function presensi()
    {
        return $this->hasMany(Presensi::class, 'id_jadwal', 'id_jadwal');
    }

    public function laporanKegiatan()
    {
        return $this->hasMany(LaporanKegiatan::class, 'id_jadwal', 'id_jadwal');
    }
}
