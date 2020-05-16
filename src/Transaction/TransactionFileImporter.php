<?php

declare(strict_types=1);

namespace Isfar\CommissionTask\Transaction;

use Isfar\CommissionTask\Entity\Transaction;
use Isfar\CommissionTask\File\File;
use Isfar\CommissionTask\File\FileReaderInterface;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

class TransactionFileImporter implements FileReaderInterface
{
    private $fileReader;
    private $mapper;

    public function __construct(
        FileReaderInterface $fileReader,
        TransactionMapper $mapper
    ) {
        $this->fileReader = $fileReader;
        $this->mapper = $mapper;
    }

    /**
     * @param File $file
     *
     * @return Transaction[]
     *
     * @throws ServiceNotFoundException
     */
    public function read(File $file): array
    {
        $content = $this->fileReader->read($file);

        return array_map(
            function ($data) use ($file) {
                return $this->mapper->map($file->getType(), $data);
            },
            $content
        );
    }
}
