<?php

declare(strict_types=1);

use Isfar\CommissionTask\Storage\ArrayStorage;
use Isfar\CommissionTask\Storage\StorageInterface;
use PHPUnit\Framework\TestCase;

class ArrayStorageTest extends TestCase
{
    /**
     * @var StorageInterface
     */
    private $store;

    public function setUp(): void
    {
        $this->store = new ArrayStorage();
    }

    public function tearDown(): void
    {
        $this->store = null;
    }

    public function testAddReturnsNullWhenUserIdNotFound(): void
    {
        $store = new ArrayStorage();
        $store->add(
            '4',
            [
                'on' => '2019-12-11',
                'amount' => '300',
            ]
        );

        $output = $this->store->get('5');
        $this->assertNull($output);
    }

    /**
     * @dataProvider dataProviderForAddTesting
     * @param string $key
     * @param array $elements
     */
    public function testAddWhenUserIdFound(
        string $key,
        array $elements
    ) {
        $store = new ArrayStorage();

        foreach ($elements as $element) {
            $store->add($key, $element);
        }

        $output = $store->get($key);

        $this->assertSame($elements, $output);
    }

    public function dataProviderForAddTesting(): array
    {
        return [
            'Adds one element' => [
                '1',
                [
                    ['amount' => '2000'],
                ],
            ],
            'Adds two consecutive elements' => [
                '4',
                [
                    ['on' => '12-12-19', 'amount' => '34.67'],
                    ['on' => '3-12-20', 'amount' => '500.45'],
                ],
            ],
        ];
    }
}
