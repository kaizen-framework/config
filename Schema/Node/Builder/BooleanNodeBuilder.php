<?php

declare(strict_types=1);

namespace Kaizen\Components\Config\Schema\Node\Builder;

use Kaizen\Components\Config\Schema\ConfigSchemaBuilder;
use Kaizen\Components\Config\Schema\Node\BooleanNode;

class BooleanNodeBuilder
{
    private ?bool $defaultValue = null;

    private bool $isRequired = false;

    public function __construct(
        private readonly string $key,
        private readonly ConfigSchemaBuilder $configSchemaBuilder
    ) {}

    public function required(): self
    {
        $this->isRequired = true;

        return $this;
    }

    public function defaultValue(bool $defaultValue): self
    {
        $this->defaultValue = $defaultValue;

        return $this;
    }

    public function buildNode(): ConfigSchemaBuilder
    {
        $booleanNode = new BooleanNode($this->key);

        if ($this->isRequired) {
            $booleanNode->required();
        }

        if (!is_null($this->defaultValue)) {
            $booleanNode->defaultValue($this->defaultValue);
        }

        $this->configSchemaBuilder->add($booleanNode);

        return $this->configSchemaBuilder;
    }
}
