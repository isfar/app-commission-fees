<?php

declare(strict_types=1);

namespace Isfar\CommissionTask\Commission;

use Evp\Component\Money\Money;
use Isfar\CommissionTask\Commission\Rule\ExemptLimit;
use Isfar\CommissionTask\Commission\Rule\Limit;
use Isfar\CommissionTask\Commission\Rule\RuleManager;
use Isfar\CommissionTask\Transaction\Transaction;
use Isfar\CommissionTask\Repository\TransactionRepository;

class Calculator implements CalculatorInterface
{
    private $transactionRepository;
    private $ruleManager;

    public function __construct(
        TransactionRepository $transactionRepository,
        RuleManager $ruleManager
    ) {
        $this->transactionRepository = $transactionRepository;
        $this->ruleManager = $ruleManager;
    }

    public function calculate(Transaction $transaction): Money
    {
        $rule = $this->ruleManager->getRuleByTransaction($transaction);
        $rate = bcdiv($rule->getRate(), '100');

        $money = $this->calculateCommissionable($transaction, $rule->getExemptLimit());

        $commission = $money->mul($rate);

        return $rule->getLimit() !== null
            ? $this->applyLimit($commission, $rule->getLimit())
            : $commission;
    }

    private function calculateCommissionable(
        Transaction $transaction,
        ?ExemptLimit $exemptLimit
    ): Money {
        $money = $transaction->getMoney();

        if ($exemptLimit === null) {
            return $money;
        }

        $date = $transaction->getOn()->setTime(0, 0, 0);

        $transactionQueryResult = $this
            ->transactionRepository
            ->getTransactionDataForCurrentWeek(
                $transaction->getUser()->getId(),
                $transaction->getType(),
                $date
            )
        ;

        if (
            $transactionQueryResult->getTotalTransactions() < $exemptLimit->getCount()
            && $transactionQueryResult->getMoney()->isLt($exemptLimit->getMoney())
        ) {
            $money = $money->sub(
                $exemptLimit->getMoney()->sub(
                    $transactionQueryResult->getMoney()
                )
            );

            $zeroMoney = Money::createZero($money->getCurrency());
            $money = $money->isLt($zeroMoney) ? $zeroMoney : $money;
        }

        return $money;
    }

    private function applyLimit(
        Money $commission,
        Limit $limit
    ) {
        $limitMoney = $limit->getMoney();

        switch ($limit->getType()) {
            case Limit::TYPE_MAX:
                return $commission->isGt($limitMoney) ? $limitMoney : $commission;
            case Limit::TYPE_MIN:
                return $commission->isLt($limitMoney) ? $limitMoney : $commission;
        }
    }
}
