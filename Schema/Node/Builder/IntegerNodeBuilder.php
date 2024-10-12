<?php

declare(strict_types=1);

namespace Kaizen\Components\Config\Schema\Node\Builder;

use Kaizen\Components\Config\Schema\ConfigSchemaBuilder;
use Kaizen\Components\Config\Schema\Node\IntegerNode;

class IntegerNodeBuilder
{
    private ?int $min = null;

    private ?int $max = null;

    private ?int $defaultValue = null;

    private bool $isRequired = false;

    public function __construct(
        private readonly string $key,
        private readonly ConfigSchemaBuilder $configSchemaBuilder
    ) {}

    public function min(int $min): self
    {
        $this->min = $min;

        return $this;
    }

    public function max(int $max): self
    {
        $this->max = $max;

        return $this;
    }

    public function defaultValue(int $defaultValue): self
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
        $integerNode = new IntegerNode($this->key);

        if (null !== $this->min) {
            $integerNode->min($this->min);
        }

        if (null !== $this->max) {
            $integerNode->max($this->max);
        }

        if (null !== $this->defaultValue) {
            $integerNode->defaultValue($this->defaultValue);
        }

        if ($this->isRequired) {
            $integerNode->required();
        }

        $this->configSchemaBuilder->add($integerNode);

        return $this->configSchemaBuilder;
    }
}
