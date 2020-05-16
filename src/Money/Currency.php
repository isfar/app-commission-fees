<?php

declare(strict_types=1);

namespace Isfar\CommissionTask\Money;

final class Currency
{
    const EUR = 'EUR';
    const USD = 'USD';
    const JPY = 'JPY';

    const DEFAULT = self::EUR;

    const ALL = [
        self::EUR,
        self::USD,
        self::JPY,
    ];
}
