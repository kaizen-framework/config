<?php

declare(strict_types=1);

namespace Kaizen\Components\Config\Schema\Node;

use Kaizen\Components\Config\Exception\InvalidNodeTypeException;

class ScalarNode extends Node
{
    public function __construct(
        private readonly string $key,
    ) {}

    #[\Override]
    public function validateType(mixed $value): void
    {
        if (!is_scalar($value)) {
            throw new InvalidNodeTypeException(sprintf(
                'The node "%s" must be of type "scalar", "%s" given',
                $this->getKey(),
                get_debug_type($value)
            ));
        }
    }

    #[\Override]
    public function getKey(): string
    {
        return $this->key;
    }
}
