<?php

declare(strict_types=1);

namespace Isfar\CommissionTask\Storage;

use Isfar\CommissionTask\Entity\Transaction;

class ArrayStorage implements StorageInterface
{
    /**
     * @var array
     */
    private $store;

    public function __construct()
    {
        $this->store = [];
    }

    public function add(string $key, $value): self
    {
        if (!array_key_exists($key, $this->store)) {
            $this->store[$key] = [];
        }

        $this->store[$key][] = $value;

        return $this;
    }

    /**
     * @param string $key
     *
     * @return Transaction[]
     */
    public function get(string $key)
    {
        return array_key_exists($key, $this->store)
            ? $this->store[$key]
            : null;
    }
}
