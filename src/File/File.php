<?php

declare(strict_types=1);

namespace Isfar\CommissionTask\File;

class File
{
    const TYPE_JSON = 'json';
    const TYPE_CSV = 'csv';

    const TYPES = [
        self::TYPE_JSON,
        self::TYPE_CSV,
    ];

    private $path;
    /**
     * @var string
     */
    private $type;

    public function __construct(?string $path = null, ?string $type = null)
    {
        $this->path = $path;
        $this->setType($type);
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        if (!in_array($type, self::TYPES, true)) {
            throw new InvalidFileTypeException('Invalid file type: ' . $type);
        }

        $this->type = $type;

        return $this;
    }
}
