<?php

namespace App\Domain\Enums;

enum StatusArtikel: string
{
    case PUBLISHED = 'published';
    case DRAFT = 'draft';
    case ARCHIVED = 'archived';
}
