<?php

declare(strict_types=1);

namespace Isfar\CommissionTask\File;

use Isfar\CommissionTask\Entity\Transaction;

class JsonReader implements FileReaderInterface
{
    /**
     * @param File $file
     *
     * @return Transaction[]
     *
     * @throws FileReaderException
     */
    public function read(File $file): array
    {
        if (
            $file->getPath() === null
            || !file_exists($file->getPath())
        ) {
            throw new FileReaderException('Invalid file path: ' . $file->getPath());
        }

        return json_decode(
            file_get_contents($file->getPath()),
            true
        );
    }
}
