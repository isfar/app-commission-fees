<?php

declare(strict_types=1);

namespace Isfar\CommissionTask\Commission\Rule;

use Evp\Component\Money\Money;

class ExemptLimit
{
    private $money;
    private $count;

    public function __construct(
        int $count,
        Money $money
    ) {
        $this->count = $count;
        $this->money = $money;
    }

    public function getMoney(): Money
    {
        return $this->money;
    }

    public function getCount(): int
    {
        return $this->count;
    }
}
