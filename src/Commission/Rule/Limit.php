<?php

declare(strict_types=1);

namespace Isfar\CommissionTask\Commission\Rule;

use Evp\Component\Money\Money;

class Limit
{
    const TYPE_MAX = 'max';
    const TYPE_MIN = 'min';

    const TYPES = [
        self::TYPE_MAX,
        self::TYPE_MIN,
    ];

    private $type;
    private $money;

    public function __construct(string $type, Money $money)
    {
        $this->type = $type;
        $this->money = $money;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getMoney(): Money
    {
        return $this->money;
    }
}
