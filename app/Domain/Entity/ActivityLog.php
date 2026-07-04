<?php

namespace App\Domain\Entity;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $table = 'activity_log';
    protected $primaryKey = 'id_log';
    public $timestamps = false;

    protected $fillable = ['id_user', 'modul', 'aktivitas', 'keterangan'];

    protected function casts(): array
    {
        return ['tanggal_log' => 'datetime'];
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }
}
