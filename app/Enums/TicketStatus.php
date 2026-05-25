<?php

namespace App\Enums;

enum TicketStatus: string
{
    case Open = 'open';
    case Assigned = 'assigned';
    case InProgress = 'in_progress';
    case PendingUser = 'pending_user';
    case PendingVendor = 'pending_vendor';
    case PendingReview = 'pending_review';
    case Returned = 'returned';
    case Closed = 'closed';

    public function label(): string
    {
        return match ($this) {
            self::Open => 'Open',
            self::Assigned => 'Assigned',
            self::InProgress => 'In Progress',
            self::PendingUser => 'Pending User',
            self::PendingVendor => 'Pending Vendor',
            self::PendingReview => 'Pending Review',
            self::Returned => 'Returned',
            self::Closed => 'Closed',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Open => 'blue',
            self::Assigned => 'indigo',
            self::InProgress => 'yellow',
            self::PendingUser => 'orange',
            self::PendingVendor => 'orange',
            self::PendingReview => 'purple',
            self::Returned => 'red',
            self::Closed => 'green',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Open => 'circle-dot',
            self::Assigned => 'user-check',
            self::InProgress => 'loader',
            self::PendingUser => 'clock',
            self::PendingVendor => 'clock',
            self::PendingReview => 'eye',
            self::Returned => 'rotate-ccw',
            self::Closed => 'check-circle',
        };
    }
}
