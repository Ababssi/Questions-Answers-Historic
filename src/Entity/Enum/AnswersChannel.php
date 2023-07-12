<?php

declare(strict_types=1);

namespace App\Entity\Enum;

enum AnswersChannel: string
{
    case FAQ = 'faq';
    case BOT = 'bot';

    public static function availableChannelsValues(): array
    {
        return array_column(self::cases(), 'value');
    }
}
