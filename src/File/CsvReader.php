<?php

declare(strict_types=1);

namespace Isfar\CommissionTask\File;

use Isfar\CommissionTask\Entity\Transaction;

class CsvReader implements FileReaderInterface
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

        return array_map(
            function ($data) {
                return str_getcsv($data);
            },
            file($file->getPath())
        );
    }
}
