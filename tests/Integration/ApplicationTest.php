<?php

declare(strict_types=1);

namespace Isfar\CommissionTask\Tests\Integration;

require_once 'vendor/autoload.php';

use Evp\Component\Money\Money;
use Isfar\CommissionTask\Application;
use Isfar\CommissionTask\File\File;
use Isfar\CommissionTask\ServiceContainerFactory;
use PHPUnit\Framework\TestCase;

class ApplicationTest extends TestCase
{
    /**
     * @dataProvider dataProviderForApplicationTesting
     * @param string $filePath
     * @param string $fileType
     * @param array $expected
     */
    public function testApplication(
        string $filePath,
        string $fileType,
        array $expected
    ) {
        bcscale(Money::DEFAULT_SCALE);

        $serviceContainerFactory = new ServiceContainerFactory();
        $serviceContainer = $serviceContainerFactory->create('config');

        /** @var Application */
        $application = $serviceContainer->get('application');

        /** @var string[] */
        $output = $application->run(new File($filePath, $fileType));
        $this->assertSame($expected, $output);
    }

    public function dataProviderForApplicationTesting()
    {
        return [
            [
                'tests/_files/input.csv',
                'csv',
                [
                    '0.60',
                    '3.00',
                    '0.00',
                    '0.06',
                    '0.90',
                    '0',
                    '0.70',
                    '0.30',
                    '0.30',
                    '5.00',
                    '0.00',
                    '0.00',
                    '8612',
                ],
            ],
            [
                'tests/_files/input.json',
                'json',
                [
                    '0.06',
                    '0.90',
                    '0',
                    '0.70',
                    '0.30',
                    '0.30',
                    '5.00',
                    '0.00',
                    '0.00',
                ],
            ],
            [
                'tests/_files/user-input.csv',
                'csv',
                [
                    '0.00',
                    '0.00',
                    '1.50',
                    '13.00',
                    '0.30',
                ],
            ],
        ];
    }
}
