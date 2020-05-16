<?php

declare(strict_types=1);

use Evp\Component\Money\Money;

require_once dirname(__DIR__) . '/vendor/autoload.php';

bcscale(Money::DEFAULT_SCALE);
