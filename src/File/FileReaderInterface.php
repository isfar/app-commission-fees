<?php

declare(strict_types=1);

namespace Isfar\CommissionTask\File;

interface FileReaderInterface
{
    public function read(File $file): array;
}
