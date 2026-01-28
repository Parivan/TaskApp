<?php

namespace App\Enum;

enum UserRole: string
{
    case ADMIN = 'admin';
    case MEMBER = 'member';

    public function getLabel(): string
    {
        return match ($this) {
            self::ADMIN => 'Admin',
            self::MEMBER => 'Member',
        };
    }
}
