<?php

declare(strict_types=1);

namespace Isfar\CommissionTask\Commission\Rule;

use Isfar\CommissionTask\Transaction\Transaction;
use Isfar\CommissionTask\Entity\User;
use Isfar\CommissionTask\File\File;
use Isfar\CommissionTask\File\FileReaderInterface;

class RuleManager
{
    private $config;
    private $ruleFactory;
    private $fileReader;

    public function __construct(
        array $config,
        RuleFactory $ruleFactory,
        FileReaderInterface $fileReader
    ) {
        $this->config = $config;
        $this->ruleFactory = $ruleFactory;
        $this->fileReader = $fileReader;
    }

    private function getRuleByUser(User $user): ?Rule
    {
        $file = new File(
            $this->config['user']['file_path'],
            File::TYPE_JSON
        );

        $ruleConfigs = $this->fileReader->read($file);

        if (isset($ruleConfigs[$user->getId()])) {
            return $this->ruleFactory->factory($ruleConfigs[$user->getId()]);
        }

        return null;
    }

    private function getDefaultRuleBy(
        string $transactionType,
        string $userType
    ): ?Rule {
        $configs = $this->config['default'];

        if (isset($configs[$transactionType][$userType])) {
            return $this->ruleFactory->factory($configs[$transactionType][$userType]);
        }

        return null;
    }

    public function getRuleByTransaction(Transaction $transaction): ?Rule
    {
        $rule = $this->getRuleByUser($transaction->getUser()) ?? $this->getDefaultRuleBy(
            $transaction->getType(),
            $transaction->getUser()->getType()
        );

        if ($rule === null) {
            throw new RuleNotFoundException('Rule not found for given transaction');
        }

        return $rule;
    }
}
