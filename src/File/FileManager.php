<?php

declare(strict_types=1);

namespace Isfar\CommissionTask\File;

use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

class FileManager implements FileReaderInterface
{
    /**
     * @var FileReaderInterface
     */
    private $fileReaders;

    public function __construct()
    {
        $this->fileReaders = [];
    }

    public function addFileReader(string $key, FileReaderInterface $fileReader): self
    {
        $this->fileReaders[$key] = $fileReader;

        return $this;
    }

    private function getFileReader(string $key): FileReaderInterface
    {
        if (isset($this->fileReaders[$key])) {
            return $this->fileReaders[$key];
        }

        throw new ServiceNotFoundException('No FileReader registered with the key: ' . $key);
    }

    public function read(File $file): array
    {
        return $this
            ->getFileReader($file->getType())
            ->read($file)
        ;
    }
}
