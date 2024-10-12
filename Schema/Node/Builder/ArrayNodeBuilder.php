<?php

declare(strict_types=1);

namespace Kaizen\Components\Config\Schema\Node\Builder;

use Kaizen\Components\Config\Schema\ConfigSchemaBuilder;
use Kaizen\Components\Config\Schema\Node\ArrayNode;
use Kaizen\Components\Config\Schema\Prototype\ConfigPrototypeInterface;

class ArrayNodeBuilder
{
    private ?ConfigPrototypeInterface $configPrototype = null;

    public function __construct(
        private readonly string $key,
        private readonly ConfigSchemaBuilder $configSchemaBuilder
    ) {}

    public function withPrototype(ConfigPrototypeInterface $configPrototype): self
    {
        $this->configPrototype = $configPrototype;

        return $this;
    }

    public function buildNode(): ConfigSchemaBuilder
    {
        $arrayNode = new ArrayNode($this->key, $this->configPrototype);

        $this->configSchemaBuilder->add($arrayNode);

        return $this->configSchemaBuilder;
    }
}
