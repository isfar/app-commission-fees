<?php
declare(strict_types=1);

namespace Isfar\CommissionTask\Tests\Commission;

use DateTimeImmutable;
use Evp\Component\Money\Money;
use Isfar\CommissionTask\Commission\Calculator;
use Isfar\CommissionTask\Commission\Rule\ExemptLimit;
use Isfar\CommissionTask\Commission\Rule\Limit;
use Isfar\CommissionTask\Commission\Rule\Rule;
use Isfar\CommissionTask\Commission\Rule\RuleManager;
use Isfar\CommissionTask\Transaction\Transaction;
use Isfar\CommissionTask\Transaction\TransactionQueryResult;
use Isfar\CommissionTask\Entity\User;
use Isfar\CommissionTask\Money\Currency;
use Isfar\CommissionTask\Repository\TransactionRepository;
use PHPUnit\Framework\TestCase;

class CalculatorTest extends TestCase
{
    /**
     * @param array $ruleConfig
     * @param string $moneyAmount
     * @param string $expectedAmount
     *
     * @dataProvider dataProviderForCalculateWithOutExemptLimitTesting
     */
    public function testCalculateWithoutExemptLimit(
        array $ruleConfig,
        string $moneyAmount,
        string $expectedAmount
    ) {
        $currency = Currency::DEFAULT;

        $rule = new Rule($ruleConfig['rate']);

        if (isset($ruleConfig['limit'])) {
            $rule->setLimit(new Limit(
                $ruleConfig['limit']['type'],
                Money::create($ruleConfig['limit']['money']['amount'], $currency)
            ));
        }

        $transactionRepository = $this->createMock(TransactionRepository::class);

        $ruleManager = $this->createMock(RuleManager::class);
        $ruleManager
            ->method('getRuleByTransaction')
            ->willReturn($rule)
        ;

        $calculator = new Calculator(
            $transactionRepository,
            $ruleManager
        );

        $transaction = new Transaction(
            Transaction::TYPE_CASH_IN,
            $this->createMock(DateTimeImmutable::class),
            Money::create($moneyAmount, $currency),
            $this->createMock(User::class)
        );

        $expected = Money::create($expectedAmount, $currency);
        $output = $calculator->calculate($transaction);

        $this->assertEquals($expected, $output);
    }

    public function dataProviderForCalculateWithOutExemptLimitTesting(): array
    {
        return [
            'Only commission rate' => [
                [
                    'rate' => '0.3',
                ],
                '100',
                '0.3',
            ],
            'Commission rate with max limit where commission is smaller than limit' => [
                [
                    'rate' => '0.03',
                    'limit' => [
                        'type' => 'max',
                        'money' => [
                            'amount' => '5',
                        ],
                    ],
                ],
                '10000',
                '3',
            ],
            'Commission rate with max limit where commission is greater than limit' => [
                [
                    'rate' => '0.03',
                    'limit' => [
                        'type' => 'max',
                        'money' => [
                            'amount' => '5',
                        ],
                    ],
                ],
                '20000',
                '5',
            ],
            'Commission rate with min limit where commission is smaller than limit' => [
                [
                    'rate' => '0.3',
                    'limit' => [
                        'type' => 'min',
                        'money' => [
                            'amount' => '0.5',
                        ],
                    ],
                ],
                '100',
                '0.5',
            ],
            'Commission rate with min limit where commission is greater than limit' => [
                [
                    'rate' => '0.3',
                    'limit' => [
                        'type' => 'min',
                        'money' => [
                            'amount' => '0.5',
                        ],
                    ],
                ],
                '200',
                '0.6',
            ],
        ];
    }

    /**
     * @param array $ruleConfig
     * @param TransactionQueryResult $transactionQueryResult
     * @param string $moneyAmount
     * @param string $expectedAmount
     *
     * @dataProvider dataProviderForCalculateWithExemptLimitTesting
     */
    public function testCalculateWithExemptLimit(
        array $ruleConfig,
        TransactionQueryResult $transactionQueryResult,
        string $moneyAmount,
        string $expectedAmount
    ) {
        $currency = Currency::EUR;

        $rule = new Rule($ruleConfig['rate']);

        $rule->setExemptLimit(new ExemptLimit(
            $ruleConfig['exempt']['count'],
            Money::create($ruleConfig['exempt']['money']['amount'], $currency)
        ));

        $ruleManager = $this->createMock(RuleManager::class);
        $ruleManager
            ->method('getRuleByTransaction')
            ->willReturn($rule)
        ;

        $transactionRepository = $this->createMock(TransactionRepository::class);
        $transactionRepository
            ->method('getTransactionDataForCurrentWeek')
            ->willReturn($transactionQueryResult)
        ;

        $calculator = new Calculator($transactionRepository, $ruleManager);

        $transaction = new Transaction(
            Transaction::TYPE_CASH_IN,
            new DateTimeImmutable('30-12-2019'),
            Money::create($moneyAmount, $currency),
            new User('1')
        );

        $output = $calculator->calculate($transaction);

        $this->assertEquals($output, Money::create($expectedAmount, $currency));

    }

    public function dataProviderForCalculateWithExemptLimitTesting(): array
    {
        $currency = Currency::EUR;

        return [
            'Max count and max limit didn\'t reach' => [
                [
                    'rate' => '0.3',
                    'exempt' => [
                        'count' => 3,
                        'money' => [
                            'amount' => '1000',
                        ],
                    ],
                ],
                new TransactionQueryResult(
                    2,
                    Money::create('800', $currency)
                ),
                '500',
                '0.9',
            ],
            'Max count reached and max limit didn\'t reach' => [
                [
                    'rate' => '0.3',
                    'exempt' => [
                        'count' => 3,
                        'money' => [
                            'amount' => '1000',
                        ],
                    ],
                ],
                new TransactionQueryResult(
                    3,
                    Money::create('800', $currency)
                ),
                '500',
                '1.5',
            ],
            'Max count and max limit both reached' => [
                [
                    'rate' => '0.3',
                    'exempt' => [
                        'count' => 3,
                        'money' => [
                            'amount' => '1000',
                        ],
                    ],
                ],
                new TransactionQueryResult(
                    3,
                    Money::create('1100', $currency)
                ),
                '500',
                '1.5',
            ],
            'Max count and max limit didn\'t reach, money is smaller than left exempt limit' => [
                [
                    'rate' => '0.3',
                    'exempt' => [
                        'count' => 3,
                        'money' => [
                            'amount' => '1000',
                        ],
                    ],
                ],
                new TransactionQueryResult(
                    2,
                    Money::create('800', $currency)
                ),
                '100',
                '0',
            ],
        ];
    }
}
