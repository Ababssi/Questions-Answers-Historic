<?php

declare(strict_types=1);

namespace App\Entity\Enum;

enum QuestionStatus: string
{
    case DRAFT = 'draft';
    case PUBLISHED = 'published';

    public static function availableStatusesValues(): array
    {
        return array_column(self::cases(), 'value');
    }
}
