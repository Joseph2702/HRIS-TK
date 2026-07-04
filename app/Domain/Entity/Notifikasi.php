<?php

namespace App\Domain\Entity;

use Illuminate\Database\Eloquent\Model;

class Notifikasi extends Model
{
    protected $table = 'notifikasi';
    protected $primaryKey = 'id_notif';
    public $timestamps = false;

    protected $fillable = ['id_user', 'judul', 'pesan', 'tipe', 'id_referensi', 'is_read'];

    protected function casts(): array
    {
        return [
            'is_read' => 'boolean',
            'created_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }
}
