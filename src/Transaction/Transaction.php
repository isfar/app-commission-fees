<?php

declare(strict_types=1);

namespace Isfar\CommissionTask\Transaction;

use DateTimeImmutable;
use Evp\Component\Money\Money;
use Isfar\CommissionTask\Entity\User;

class Transaction
{
    const TYPE_CASH_OUT = 'cash_out';
    const TYPE_CASH_IN = 'cash_in';

    const TYPES = [
        self::TYPE_CASH_OUT,
        self::TYPE_CASH_IN,
    ];

    private $type;
    private $on;
    private $money;
    private $user;

    public function __construct(
        string $type,
        DateTimeImmutable $dateTimeImmutable,
        Money $money,
        User $user
    ) {
        $this->type = $type;
        $this->on = $dateTimeImmutable;
        $this->money = $money;
        $this->user = $user;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function setOn(DateTimeImmutable $dateTimeImmutable): self
    {
        $this->on = $dateTimeImmutable;

        return $this;
    }

    public function getOn(): DateTimeImmutable
    {
        return $this->on;
    }

    public function setMoney(Money $money): self
    {
        $this->money = $money;

        return $this;
    }

    public function getMoney(): Money
    {
        return $this->money;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getUser(): User
    {
        return $this->user;
    }
}
