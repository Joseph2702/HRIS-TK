<?php

namespace App\Domain\Entity;

use App\Domain\Enums\StatusArtikel;
use Illuminate\Database\Eloquent\Model;

class Artikel extends Model
{
    protected $table = 'artikel';
    protected $primaryKey = 'id_artikel';
    public $timestamps = false;

    protected $fillable = [
        'id_user', 'judul_artikel', 'gambar_artikel', 'gambar_artikel_2',
        'konten_artikel', 'status_artikel', 'tipe', 'tanggal_publish',
    ];

    protected function casts(): array
    {
        return [
            'status_artikel' => StatusArtikel::class,
            'tanggal_publish' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function pembuat()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }
}
