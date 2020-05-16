<?php

declare(strict_types=1);

namespace Isfar\CommissionTask\Commission\Rule;

class Rule
{
    private $rate;

    /**
     * @var Limit
     */
    private $limit;

    /**
     * @var ExemptLimit
     */
    private $exemptLimit;

    public function __construct(string $rate = null)
    {
        $this->rate = $rate;
    }

    public function getRate(): string
    {
        return $this->rate;
    }

    public function setLimit(?Limit $limit): self
    {
        $this->limit = $limit;

        return $this;
    }

    public function getLimit(): ?Limit
    {
        return $this->limit;
    }

    public function setExemptLimit(?ExemptLimit $exemptLimit): self
    {
        $this->exemptLimit = $exemptLimit;

        return $this;
    }

    public function getExemptLimit(): ?ExemptLimit
    {
        return $this->exemptLimit;
    }
}
