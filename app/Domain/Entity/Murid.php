<?php

namespace App\Domain\Entity;

use App\Domain\Enums\StatusMurid;
use Illuminate\Database\Eloquent\Model;

class Murid extends Model
{
    protected $table = 'murid';
    protected $primaryKey = 'id_murid';
    public $timestamps = false;

    protected $fillable = [
        'id_orang_tua', 'id_kelas', 'nama_murid', 'tanggal_lahir',
        'jenis_kelamin', 'foto_murid', 'status_murid',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_lahir' => 'date',
            'status_murid' => StatusMurid::class,
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function orangTua()
    {
        return $this->belongsTo(OrangTua::class, 'id_orang_tua', 'id_orang_tua');
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'id_kelas', 'id_kelas');
    }

    public function presensi()
    {
        return $this->hasMany(Presensi::class, 'id_murid', 'id_murid');
    }

    public function laporanKegiatan()
    {
        return $this->hasMany(LaporanKegiatan::class, 'id_murid', 'id_murid');
    }
}
