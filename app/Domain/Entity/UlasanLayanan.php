<?php

namespace App\Domain\Entity;

use Illuminate\Database\Eloquent\Model;

class UlasanLayanan extends Model
{
    protected $table = 'ulasan_layanan';
    protected $primaryKey = 'id_ulasan';
    public $timestamps = false;

    protected $fillable = [
        'id_artikel',
        'id_user',
        'rating',
        'isi_ulasan',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    public function artikel()
    {
        return $this->belongsTo(Artikel::class, 'id_artikel', 'id_artikel');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }
}
