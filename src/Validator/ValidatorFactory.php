<?php

declare(strict_types=1);

namespace Isfar\CommissionTask\Validator;

use Symfony\Component\Validator\Validation;

class ValidatorFactory
{
    private $configYamlPath;

    public function __construct(string $configYamlPath)
    {
        $this->configYamlPath = $configYamlPath;
    }

    public function create()
    {
        return Validation::createValidatorBuilder()
            ->addYamlMapping($this->configYamlPath)
            ->getValidator()
        ;
    }
}
