<?php

declare(strict_types=1);

namespace Isfar\CommissionTask\Tests\Money;

use Evp\Component\Money\Money;
use Isfar\CommissionTask\Money\Currency;
use Isfar\CommissionTask\Money\CurrencyConverter;
use PHPUnit\Framework\TestCase;

class CurrencyConverterTest extends TestCase
{
    /**
     * @dataProvider dataProviderForConvertTesting
     * @param Money $money
     * @param string $destinationCurrency
     * @param string $expected
     */
    public function testConvert(
        Money $money,
        string $destinationCurrency,
        string $expected
    ) {
        $config = [
            Currency::USD => '1.1491',
            Currency::JPY => '129.53',
            Currency::EUR => '1.00',
        ];

        $converter = new CurrencyConverter($config);

        $output = $converter->convert(
            $money,
            $destinationCurrency
        );

        $this->assertSame($expected, $output->getAmount());
        $this->assertSame($destinationCurrency, $output->getCurrency());
    }

    public function dataProviderForConvertTesting()
    {
        return [
            'USD to JPY' => [new Money('100.87', Currency::USD), Currency::JPY, '11370.369010'],
            'USD to USD' => [new Money('100.87', Currency::USD), Currency::USD, '100.870000'],
            'EUR to JPY' => [new Money('500.86', Currency::EUR), Currency::JPY, '64876.395800'],
            'USD to EUR' => [new Money('100.87', Currency::USD), Currency::EUR, '87.781714'],
        ];
    }
}
