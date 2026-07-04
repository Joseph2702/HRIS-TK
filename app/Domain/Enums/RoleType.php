<?php

namespace App\Domain\Enums;

enum RoleType: string
{
    case ADMIN = 'admin';
    case GURU = 'guru';
    case ORANG_TUA = 'orang_tua';
}
