<?php

declare(strict_types=1);

namespace Isfar\CommissionTask\Transaction;

use Evp\Component\Money\Money;

class TransactionQueryResult
{
    private $totalTransactions;
    private $money;

    public function __construct(
        int $totalTransactions,
        Money $money
    ) {
        $this->totalTransactions = $totalTransactions;
        $this->money = $money;
    }

    public function getTotalTransactions(): int
    {
        return $this->totalTransactions;
    }

    public function setTotalTransactions(int $totalTransactions): self
    {
        $this->totalTransactions = $totalTransactions;

        return $this;
    }

    public function getMoney(): Money
    {
        return $this->money;
    }

    public function setMoney(Money $money): self
    {
        $this->money = $money;

        return $this;
    }
}
