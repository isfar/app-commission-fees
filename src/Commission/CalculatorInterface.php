<?php

declare(strict_types=1);

namespace Isfar\CommissionTask\Commission;

use Evp\Component\Money\Money;
use Isfar\CommissionTask\Transaction\Transaction;

interface CalculatorInterface
{
    public function calculate(Transaction $transaction): Money;
}
