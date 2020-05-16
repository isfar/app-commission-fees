<?php

declare(strict_types=1);

namespace Isfar\CommissionTask\Transaction\Mapper;

use DateTimeImmutable;
use Evp\Component\Money\Money;
use Isfar\CommissionTask\Transaction\Transaction;
use Isfar\CommissionTask\Entity\User;

class JsonMapper implements MapperInterface
{
    public function map(array $data): Transaction
    {
        return new Transaction(
            $data['operation_type'],
            new DateTimeImmutable($data['operation_date']),
            Money::create($data['amount'], $data['currency']),
            new User($data['user_id'], $data['user_type'])
        );
    }
}
