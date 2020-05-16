<?php

declare(strict_types=1);

namespace Isfar\CommissionTask\Money;

use Evp\Component\Money\Money;

class CurrencyConverter
{
    private $conversionRates;

    public function __construct(array $conversionRates)
    {
        $this->conversionRates = $conversionRates;
    }

    public function convert(
        Money $money,
        string $destinationCurrency
    ): Money {
        $amount = bcmul(
            $money->getAmount(),
            bcdiv(
                $this->conversionRates[$destinationCurrency],
                $this->conversionRates[$money->getCurrency()],
                Money::DEFAULT_SCALE
            )
        );

        return new Money($amount, $destinationCurrency);
    }
}
