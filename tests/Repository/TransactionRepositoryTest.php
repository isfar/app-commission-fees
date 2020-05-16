<?php

declare(strict_types=1);

namespace Isfar\CommissionTask\Tests\Repository;

use DateTimeImmutable;
use Evp\Component\Money\Money;
use Isfar\CommissionTask\Transaction\Transaction;
use Isfar\CommissionTask\Transaction\TransactionQueryResult;
use Isfar\CommissionTask\Entity\User;
use Isfar\CommissionTask\Money\Currency;
use Isfar\CommissionTask\Repository\TransactionRepository;
use Isfar\CommissionTask\Storage\StorageInterface;
use PHPUnit\Framework\TestCase;

class TransactionRepositoryTest extends TestCase
{
    /**
     * @param array $storageReturns
     * @param TransactionQueryResult $expected
     *
     * @dataProvider dataProviderForGetTransactionDataForCurrentWeekTesting
     */
    public function testGetTransactionDataForCurrentWeek(
        ?array $storageReturns,
        TransactionQueryResult $expected
    ) {
        $userId = '1';
        $transactionType = 'cash_out';

        $storage = $this->createMock(StorageInterface::class);
        $storage
            ->expects($this->once())
            ->method('get')
            ->with($userId . $transactionType)
            ->willReturn($storageReturns)
        ;

        $transactionRepository = new TransactionRepository($storage);

        $output = $transactionRepository->getTransactionDataForCurrentWeek(
            $userId,
            $transactionType,
            new DateTimeImmutable('2019-12-03')
        );

        $this->assertEquals($expected, $output);
    }

    public function dataProviderForGetTransactionDataForCurrentWeekTesting(): array
    {
        $currency = Currency::DEFAULT;
        $user = $this->createMock(User::class);

        return [
            [
                null,
                new TransactionQueryResult(
                    0,
                    Money::createZero($currency)
                ),
            ],
            [
                [
                    new Transaction(
                        Transaction::TYPE_CASH_IN,
                        new DateTimeImmutable('2019-12-04'),
                        Money::create('200.123456', $currency),
                        $user
                    ),
                    new Transaction(
                        Transaction::TYPE_CASH_OUT,
                        new DateTimeImmutable('2019-12-03'),
                        Money::create('500.111132', $currency),
                        $user
                    ),
                ],
                new TransactionQueryResult(
                    2,
                    new Money('700.234588', $currency)
                ),
            ],
        ];
    }
}
