<?php

declare(strict_types=1);

namespace Isfar\CommissionTask\Tests\Commission\Rule;

use DateTimeImmutable;
use Evp\Component\Money\Money;
use Isfar\CommissionTask\Commission\Rule\Limit;
use Isfar\CommissionTask\Commission\Rule\Rule;
use Isfar\CommissionTask\Commission\Rule\RuleFactory;
use Isfar\CommissionTask\Commission\Rule\RuleManager;
use Isfar\CommissionTask\Commission\Rule\RuleNotFoundException;
use Isfar\CommissionTask\Entity\User;
use Isfar\CommissionTask\File\File;
use Isfar\CommissionTask\File\FileManager;
use Isfar\CommissionTask\File\JsonReader;
use Isfar\CommissionTask\Money\Currency;
use Isfar\CommissionTask\Transaction\Transaction;
use PHPUnit\Framework\TestCase;

class RuleManagerTest extends TestCase
{
    /**
     * @var RuleManager
     */
    private $ruleManager;

    public function setUp(): void
    {
        $ruleFactory = new RuleFactory();
        $fileReader = new FileManager();
        $fileReader->addFileReader(
            File::TYPE_JSON,
            new JsonReader()
        );

        $this->ruleManager = new RuleManager(
            [
                'user' => [
                    'file_path' => 'tests/_files/data/user-rules.json',
                ],
                'default' => [
                    'cash_in' => [
                        'legal' => [
                            'rate' => '0.03',
                            'limit' => [
                                'type' => Limit::TYPE_MAX,
                                'money' => [
                                    'amount' => '5'
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            $ruleFactory,
            $fileReader
        );
    }

    public function tearDown(): void
    {
        $this->ruleManager = null;
    }

    public function testGetRuleByUserReturnsRuleIfRuleIsNotFound(): void
    {
        $userId = '10';
        $rule = $this->ruleManager->getRuleByUser(new User($userId));

        $this->assertNull($rule);
    }

    public function testGetDefaultRuleWhenRuleIsNotFound(): void
    {
        $rule = $this
            ->ruleManager
            ->getDefaultRuleBy(Transaction::TYPE_CASH_OUT, User::TYPE_LEGAL)
        ;

        $this->assertNull($rule);
    }

    /**
     * @param string $userId
     * @param string $transactionType
     * @param string $userType
     * 
     * @dataProvider dataProviderForGetRuleByTransactionWhenRuleIsNotFoundTesting
     */
    public function testGetRuleByTransactionWhenRuleIsNotFound(
        string $userId,
        string $transactionType,
        string $userType
    ): void {
        $this->expectException(RuleNotFoundException::class);

        $transaction = new Transaction(
            $transactionType,
            new DateTimeImmutable(), // dummy
            Money::createZero(Currency::DEFAULT), // dummy
            new User($userId, $userType)
        );

        $this->ruleManager->getRuleByTransaction($transaction);
    }

    public function dataProviderForGetRuleByTransactionWhenRuleIsNotFoundTesting(): array
    {
        return [
            ['1', 'non_existent_trx_type', 'non_existent_user_type'],
        ];
    }


    /**
     * @param null|string $userId
     * @param null|string $transactionType
     * @param null|string $userType
     * 
     * @dataProvider dataProviderForGetRuleByTransactionWhenRuleIsFoundTesting
     */
    public function testGetRuleByTransactionWhenRuleIsFound(
        ?string $userId,
        ?string $transactionType = null,
        ?string $userType = null
    ): void {
        $transaction = new Transaction(
            $transactionType ?? Transaction::TYPE_CASH_IN,
            new DateTimeImmutable(), // dummy
            Money::createZero(Currency::DEFAULT), // dummy
            new User($userId ?? '1', $userType)
        );

        $rule = $this->ruleManager->getRuleByTransaction($transaction);

        $this->assertInstanceOf(Rule::class, $rule);
    }

    public function dataProviderForGetRuleByTransactionWhenRuleIsFoundTesting(): array
    {
        return [
            ['11'],
            ['111'],
            ['1111'],
            [null, Transaction::TYPE_CASH_IN, User::TYPE_LEGAL],
        ];
    }
}

