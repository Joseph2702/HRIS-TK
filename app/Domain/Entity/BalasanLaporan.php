<?php

namespace App\Domain\Entity;

use Illuminate\Database\Eloquent\Model;

class BalasanLaporan extends Model
{
    protected $table = 'balasan_laporan';
    protected $primaryKey = 'id_balasan';
    public $timestamps = false;

    protected $fillable = ['id_laporan', 'id_user', 'isi_balasan'];

    protected function casts(): array
    {
        return ['created_at' => 'datetime'];
    }

    public function laporan()
    {
        return $this->belongsTo(LaporanKegiatan::class, 'id_laporan', 'id_laporan');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }
}
