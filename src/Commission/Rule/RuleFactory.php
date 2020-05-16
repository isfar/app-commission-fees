<?php

declare(strict_types=1);

namespace Isfar\CommissionTask\Commission\Rule;

use Evp\Component\Money\Money;
use Isfar\CommissionTask\Money\Currency;
use Symfony\Component\Console\Exception\InvalidOptionException;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;

class RuleFactory
{
    /**
     * @param array $config
     *
     * @return Rule
     *
     * @throws InvalidOptionException
     */
    public function factory(array $config): Rule
    {
        if (!isset($config['rate'])) {
            throw new MissingOptionsException('Missing rule option: `rate`');
        }

        $rule = new Rule($config['rate']);

        if (isset($config['limit'])) {
            $limitConfig = $config['limit'];

            if (
                !isset($limitConfig['type'])
                || !isset($limitConfig['money']['amount'])
            ) {
                throw new MissingOptionsException('Missing rule option(s): `type` or `money`');
            }

            if (!in_array($limitConfig['type'], Limit::TYPES, true)) {
                throw new InvalidOptionException('Unsupported Limit type: "' . $limitConfig['type'] . '"');
            }

            $rule->setlimit(new Limit(
                $limitConfig['type'],
                Money::create(
                    $limitConfig['money']['amount'],
                    $limitConfig['money']['currency'] ?? Currency::DEFAULT
                )
            ));
        }

        if (isset($config['exempt'])) {
            $exemptConfig = $config['exempt'];

            if (
                !isset($exemptConfig['count'])
                || !isset($exemptConfig['money']['amount'])
            ) {
                throw new MissingOptionsException('Missing rule option(s): `count` or `money`');
            }

            $rule->setExemptLimit(new ExemptLimit(
                $exemptConfig['count'],
                Money::create(
                    $exemptConfig['money']['amount'],
                    $exemptConfig['money']['currency'] ?? Currency::DEFAULT
                )
            ));
        }

        return $rule;
    }
}
