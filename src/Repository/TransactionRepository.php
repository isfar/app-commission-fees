<?php

declare(strict_types=1);

namespace Isfar\CommissionTask\Repository;

use DateTimeImmutable;
use Evp\Component\Money\Money;
use Isfar\CommissionTask\Transaction\Transaction;
use Isfar\CommissionTask\Transaction\TransactionQueryResult;
use Isfar\CommissionTask\Money\Currency;
use Isfar\CommissionTask\Storage\StorageInterface;

class TransactionRepository
{
    private $storage;

    public function __construct(StorageInterface $storage)
    {
        $this->storage = $storage;
    }

    public function store(Transaction $transaction)
    {
        $this->storage->add(
            $transaction->getUser()->getId() . $transaction->getType(),
            $transaction
        );

        return $this;
    }

    public function getTransactionDataForCurrentWeek(
        string $userId,
        string $transactionType,
        DateTimeImmutable $date
    ): TransactionQueryResult {
        $firstDayInWeek = $date->modify('Monday this week')->setTime(0, 0, 0);

        /** @var Transaction[] */
        $transactions = $this->storage->get($userId . $transactionType);

        $money = Money::createZero(Currency::DEFAULT);

        if ($transactions === null) {
            return new TransactionQueryResult(0, $money);
        }

        $numTransactions = 0;

        foreach ($transactions as $transaction) {
            $date = $transaction->getOn();

            if ($date < $firstDayInWeek) {
                break;
            }

            $money = $money->add($transaction->getMoney());
            $numTransactions++;
        }

        return new TransactionQueryResult($numTransactions, $money);
    }
}
