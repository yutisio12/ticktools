<?php

namespace App\Enums;

enum TicketCategory: string
{
    case Request = 'request';
    case Incident = 'incident';
    case Change = 'change';

    public function label(): string
    {
        return match ($this) {
            self::Request => 'Request',
            self::Incident => 'Incident',
            self::Change => 'Change',
        };
    }

    public function baseScore(): int
    {
        return match ($this) {
            self::Request => 2,
            self::Incident => 3,
            self::Change => 5,
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Request => 'blue',
            self::Incident => 'orange',
            self::Change => 'purple',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Request => 'inbox',
            self::Incident => 'alert-triangle',
            self::Change => 'git-branch',
        };
    }
}
