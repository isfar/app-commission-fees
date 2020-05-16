<?php

declare(strict_types=1);

namespace Isfar\CommissionTask\Storage;

interface StorageInterface
{
    public function add(string $key, $value);

    public function get(string $key);
}
