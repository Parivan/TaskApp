<?php

namespace App\Enum;

enum TaskPriority: string
{
    case LOW = 'low';
    case MEDIUM = 'medium';
    case HIGH = 'high';

    public function getLabel(): string
    {
        return match ($this) {
            self::LOW => 'Low',
            self::MEDIUM => 'Medium',
            self::HIGH => 'High',
        };
    }
}
