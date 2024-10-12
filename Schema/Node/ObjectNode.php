<?php

declare(strict_types=1);

namespace Kaizen\Components\Config\Schema\Node;

use Kaizen\Components\Config\Exception\InvalidNodeTypeException;
use Kaizen\Components\Config\Schema\ConfigSchema;

class ObjectNode extends Node implements ParentNodeInterface
{
    public function __construct(
        private readonly string $key,
        private readonly ?ConfigSchema $configSchema = null,
    ) {}

    #[\Override]
    public function validateType(mixed $value): void
    {
        if (!is_array($value)) {
            throw new InvalidNodeTypeException(sprintf(
                'The node "%s" must be of type "object", "%s" given',
                $this->getKey(),
                get_debug_type($value)
            ));
        }

        if (null === $this->configSchema) {
            return;
        }

        foreach ($value as $key => $currentValue) {
            $node = $this->configSchema->getNode($key);

            if (null === $node) {
                throw new InvalidNodeTypeException(sprintf(
                    'key %s not allowed within the %s node, allowed keys : %s',
                    $key,
                    $this->getKey(),
                    implode(', ', $this->configSchema->getNodeKeys())
                ));
            }

            $node->validateType($currentValue);
        }
    }

    #[\Override]
    public function getKey(): string
    {
        return $this->key;
    }

    #[\Override]
    public function getChildren(): ConfigSchema
    {
        if (null === $this->configSchema) {
            throw new \RuntimeException(sprintf(
                'Can not call "%s::getChildren()" no children',
                self::class
            ));
        }

        return $this->configSchema;
    }
}
