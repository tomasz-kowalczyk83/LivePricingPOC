<?php

namespace App\Enums;

enum QuoteResponseStatusEnum: string
{
    case PENDING = 'pending';
    case SUBMITTED = 'submitted';
    case DECLINED = 'declined';
    case TIMEOUT = 'timeout';
    case ACCEPTED = 'accepted';
    case REJECTED = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::SUBMITTED => 'Submitted',
            self::DECLINED => 'Declined',
            self::TIMEOUT => 'Timeout',
            self::ACCEPTED => 'Accepted',
            self::REJECTED => 'Rejected',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::PENDING => 'gray',
            self::SUBMITTED => 'info',
            self::DECLINED => 'warning',
            self::TIMEOUT => 'danger',
            self::ACCEPTED => 'success',
            self::REJECTED => 'danger',
        };
    }
}
