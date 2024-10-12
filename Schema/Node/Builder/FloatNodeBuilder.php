<?php

declare(strict_types=1);

namespace Kaizen\Components\Config\Schema\Node\Builder;

use Kaizen\Components\Config\Schema\ConfigSchemaBuilder;
use Kaizen\Components\Config\Schema\Node\FloatNode;

class FloatNodeBuilder
{
    private ?float $min = null;

    private ?float $max = null;

    private ?float $defaultValue = null;

    private bool $isRequired = false;

    public function __construct(
        private readonly string $key,
        private readonly ConfigSchemaBuilder $configSchemaBuilder
    ) {}

    public function min(float $min): self
    {
        $this->min = $min;

        return $this;
    }

    public function max(float $max): self
    {
        $this->max = $max;

        return $this;
    }

    public function defaultValue(float $defaultValue): self
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
        $floatNode = new FloatNode($this->key);

        if (!is_null($this->min)) {
            $floatNode->min($this->min);
        }

        if (!is_null($this->max)) {
            $floatNode->max($this->max);
        }

        if (!is_null($this->defaultValue)) {
            $floatNode->defaultValue($this->defaultValue);
        }

        if ($this->isRequired) {
            $floatNode->required();
        }

        $this->configSchemaBuilder->add($floatNode);

        return $this->configSchemaBuilder;
    }
}
