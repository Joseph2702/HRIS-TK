<?php

namespace App\Domain\Entity;

use App\Domain\Enums\StatusMurid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Murid extends Model
{
    protected $table = 'murid';
    protected $primaryKey = 'id_murid';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'id_murid', 'id_orang_tua', 'id_kelas', 'nama_murid', 'tanggal_lahir',
        'jenis_kelamin', 'foto_murid', 'status_murid',
    ];

    protected function casts(): array
    {
        return [
            'id_murid' => 'string',
            'tanggal_lahir' => 'date',
            'status_murid' => StatusMurid::class,
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Murid $murid) {
            if (! $murid->id_murid) {
                $year = now()->format('Y');
                $counter = static::getNextCounterForYear($year);
                $murid->id_murid = $year . '-' . str_pad($counter, 3, '0', STR_PAD_LEFT);
            }
        });
    }

    private static function getNextCounterForYear(string $year): int
    {
        $last = DB::table('murid')
            ->where('id_murid', 'like', $year . '-%')
            ->orderBy('id_murid', 'desc')
            ->value('id_murid');

        if (! $last) {
            return 1;
        }

        $parts = explode('-', $last);
        $counter = isset($parts[1]) ? (int) $parts[1] : 0;

        return $counter + 1;
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
