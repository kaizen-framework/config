<?php

declare(strict_types=1);

namespace Kaizen\Components\Config\Schema\Node\Builder;

use Kaizen\Components\Config\Schema\ConfigSchemaBuilder;
use Kaizen\Components\Config\Schema\Node\StringNode;

class StringNodeBuilder
{
    private bool $isRequired = false;

    private ?string $defaultValue = null;

    public function __construct(
        private readonly string $key,
        private readonly ConfigSchemaBuilder $configSchemaBuilder
    ) {}

    public function required(): self
    {
        $this->isRequired = true;

        return $this;
    }

    public function defaultValue(string $defaultValue): self
    {
        $this->defaultValue = $defaultValue;

        return $this;
    }

    public function buildNode(): ConfigSchemaBuilder
    {
        $stringNode = new StringNode($this->key);

        if (null !== $this->defaultValue) {
            $stringNode->defaultValue($this->defaultValue);
        }

        if ($this->isRequired) {
            $stringNode->required();
        }

        $this->configSchemaBuilder->add($stringNode);

        return $this->configSchemaBuilder;
    }
}
