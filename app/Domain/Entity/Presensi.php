<?php

namespace App\Domain\Entity;

use App\Domain\Enums\StatusKehadiran;
use Illuminate\Database\Eloquent\Model;

class Presensi extends Model
{
    protected $table = 'presensi';
    protected $primaryKey = 'id_presensi';
    public $timestamps = false;

    protected $fillable = [
        'id_jadwal', 'id_murid', 'status_kehadiran', 'keterangan', 'dicatat_oleh',
    ];

    protected function casts(): array
    {
        return [
            'status_kehadiran' => StatusKehadiran::class,
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

    public function pencatat()
    {
        return $this->belongsTo(User::class, 'dicatat_oleh', 'id_user');
    }
}
