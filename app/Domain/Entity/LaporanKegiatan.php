<?php

namespace App\Domain\Entity;

use Illuminate\Database\Eloquent\Model;

class LaporanKegiatan extends Model
{
    protected $table = 'laporan_kegiatan';
    protected $primaryKey = 'id_laporan';
    public $timestamps = false;

    protected $fillable = ['id_jadwal', 'id_murid', 'id_guru', 'judul_laporan', 'isi_laporan', 'indikator', 'indikator_catatan'];

    protected function casts(): array
    {
        return [
            'id_murid' => 'string',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function jadwal()
    {
        return $this->belongsTo(JadwalKelas::class, 'id_jadwal', 'id_jadwal');
    }

    public function murid()
    {
        return $this->belongsTo(Murid::class, 'id_murid', 'id_murid');
    }

    public function guru()
    {
        return $this->belongsTo(Guru::class, 'id_guru', 'id_guru');
    }

    public function balasan()
    {
        return $this->hasMany(BalasanLaporan::class, 'id_laporan', 'id_laporan');
    }
}
