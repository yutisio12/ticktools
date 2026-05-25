<?php

namespace App\Enums;

enum Impact: string
{
    case Personal = 'personal';
    case Department = 'department';
    case CompanyWide = 'company_wide';

    public function label(): string
    {
        return match ($this) {
            self::Personal => 'Personal',
            self::Department => 'Department',
            self::CompanyWide => 'Company Wide',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Personal => 'gray',
            self::Department => 'yellow',
            self::CompanyWide => 'red',
        };
    }

    public function score(): int
    {
        return match ($this) {
            self::Personal => 1,
            self::Department => 3,
            self::CompanyWide => 5,
        };
    }
}
