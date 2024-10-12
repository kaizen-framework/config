<?php

namespace Kaizen\Components\Config\Schema\Node;

use Kaizen\Components\Config\Exception\InvalidNodeTypeException;

class StringNode extends Node
{
    public function __construct(
        private readonly string $key,
    ) {}

    #[\Override]
    public function validateType(mixed $value): void
    {
        if (!is_string($value)) {
            throw new InvalidNodeTypeException(sprintf(
                'The node "%s" must be of type "string", "%s" given',
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
