<?php

namespace App\Domain\Entity;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $table = 'appointments';
    protected $primaryKey = 'id_appointment';
    public $timestamps = true;

    protected $fillable = [
        'id_orang_tua',
        'id_murid',
        'id_jadwal',
        'from_date',
        'to_date',
        'indikator_threshold_rule',
        'reason',
        'status',
        'assigned_guru_id',
        'approved_by',
    ];

    protected function casts(): array
    {
        return [
            'id_murid' => 'string',
            'from_date' => 'date',
            'to_date' => 'date',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function orangTua()
    {
        return $this->belongsTo(OrangTua::class, 'id_orang_tua', 'id_orang_tua');
    }

    public function murid()
    {
        return $this->belongsTo(Murid::class, 'id_murid', 'id_murid');
    }

    public function guru()
    {
        return $this->belongsTo(Guru::class, 'assigned_guru_id', 'id_guru');
    }

    public function approvedUser()
    {
        return $this->belongsTo(User::class, 'approved_by', 'id_user');
    }
}

