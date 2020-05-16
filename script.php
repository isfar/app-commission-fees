<?php

declare(strict_types=1);

use Evp\Component\Money\Money;
use Isfar\CommissionTask\Application;
use Isfar\CommissionTask\Application\UnsupportedFileTypeException;
use Isfar\CommissionTask\File\File;
use Isfar\CommissionTask\ServiceContainerFactory;

require_once 'vendor/autoload.php';

bcscale(Money::DEFAULT_SCALE);

$serviceContainerFactory = new ServiceContainerFactory();
$serviceContainer = $serviceContainerFactory->create('config');

/** @var Application */
$application = $serviceContainer->get('application');
$defaultFileType = 'csv';

try {
    /** @var string[] */
    $amounts = $application
        ->run(new File(
            $argv[1],
            $argv[2] ?? $defaultFileType
        ));

    $output =  implode(PHP_EOL, $amounts);
} catch (UnsupportedFileTypeException $unsupportedFileTypeException) {
    $output =  $unsupportedFileTypeException->getMessage();
}

echo $output.PHP_EOL;
