<?php

declare(strict_types=1);

namespace Isfar\CommissionTask\Transaction\Mapper;

use Isfar\CommissionTask\Transaction\Transaction;

interface MapperInterface
{
    public function map(array $data): Transaction;
}
