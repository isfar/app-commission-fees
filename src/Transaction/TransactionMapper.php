<?php

declare(strict_types=1);

namespace Isfar\CommissionTask\Transaction;

use Isfar\CommissionTask\Transaction\Mapper\MapperInterface;
use Isfar\CommissionTask\Transaction\Mapper\MapperNotFoundException;

class TransactionMapper
{
    /**
     * @var MapperInterface[]
     */
    private $mappers;

    public function __construct()
    {
        $this->mappers = [];
    }

    public function addMapper(string $type, MapperInterface $mapper)
    {
        $this->mappers[$type] = $mapper;

        return $this;
    }

    public function map(string $type, array $data)
    {
        if (!isset($this->mappers[$type])) {
            throw new MapperNotFoundException('No mappered registered for the type: ' . $type);
        }

        return $this->mappers[$type]->map($data);
    }
}
