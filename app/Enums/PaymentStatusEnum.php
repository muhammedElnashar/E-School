<?php
namespace App\Enums;

enum PaymentStatusEnum: string
{
    case Pending = 'pending';
    case Completed = 'completed';
    case Failed = 'failed';
    case Refunded = 'refunded';
    case Cancelled = 'cancelled';

    public function isFinal(): bool
    {
        return in_array($this, [self::Completed, self::Failed, self::Refunded, self::Cancelled]);
    }
}
