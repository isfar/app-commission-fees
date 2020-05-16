<?php

declare(strict_types=1);

namespace Isfar\CommissionTask\Entity;

class User
{
    const TYPE_NATURAL = 'natural';
    const TYPE_LEGAL = 'legal';

    const TYPES = [
        self::TYPE_NATURAL,
        self::TYPE_LEGAL,
    ];

    private $id;
    private $type;

    public function __construct(string $id, ?string $type = null)
    {
        $this->id = $id;
        $this->type = $type;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }
}
