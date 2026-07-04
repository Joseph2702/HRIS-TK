<?php

namespace App\Domain\Enums;

enum StatusKehadiran: string
{
    case HADIR = 'hadir';
    case TIDAK_HADIR = 'tidak_hadir';
    case IZIN = 'izin';
    case SAKIT = 'sakit';
}
