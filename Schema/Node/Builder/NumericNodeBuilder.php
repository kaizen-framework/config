<?php

declare(strict_types=1);

namespace Kaizen\Components\Config\Schema\Node\Builder;

use Kaizen\Components\Config\Schema\ConfigSchemaBuilder;
use Kaizen\Components\Config\Schema\Node\NumericNode;

class NumericNodeBuilder
{
    private null|float|int $min = null;

    private null|float|int $max = null;

    private null|float|int $defaultValue = null;

    private bool $isRequired = false;

    public function __construct(
        private readonly string $key,
        private readonly ConfigSchemaBuilder $configSchemaBuilder
    ) {}

    public function min(float|int $min): self
    {
        $this->min = $min;

        return $this;
    }

    public function max(float|int $max): self
    {
        $this->max = $max;

        return $this;
    }

    public function defaultValue(float|int $defaultValue): self
    {
        $this->defaultValue = $defaultValue;

        return $this;
    }

    public function required(): self
    {
        $this->isRequired = true;

        return $this;
    }

    public function buildNode(): ConfigSchemaBuilder
    {
        $numericNode = new NumericNode($this->key);

        if (null !== $this->min) {
            $numericNode->min($this->min);
        }

        if (null !== $this->max) {
            $numericNode->max($this->max);
        }

        if (null !== $this->defaultValue) {
            $numericNode->defaultValue($this->defaultValue);
        }

        if ($this->isRequired) {
            $numericNode->required();
        }

        $this->configSchemaBuilder->add($numericNode);

        return $this->configSchemaBuilder;
    }
}
