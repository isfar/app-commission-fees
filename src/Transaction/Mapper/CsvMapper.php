<?php

declare(strict_types=1);

namespace Isfar\CommissionTask\Transaction\Mapper;

use DateTimeImmutable;
use Evp\Component\Money\Money;
use Isfar\CommissionTask\Transaction\Transaction;
use Isfar\CommissionTask\Entity\User;

class CsvMapper implements MapperInterface
{
    public function map(array $data): Transaction
    {
        return new Transaction(
            $data[3],
            new DateTimeImmutable($data[0]),
            new Money($data[4], $data[5]),
            new User($data[1], $data[2])
        );
    }
}
