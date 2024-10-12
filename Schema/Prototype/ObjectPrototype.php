<?php

namespace Kaizen\Components\Config\Schema\Prototype;

use Kaizen\Components\Config\Exception\InvalidNodeTypeException;
use Kaizen\Components\Config\Exception\InvalidSchemaException;
use Kaizen\Components\Config\Schema\ConfigSchema;
use Kaizen\Components\Config\Schema\Node\Node;

class ObjectPrototype extends AbstractPrototype
{
    private readonly ?ConfigSchema $configSchema;

    /**
     * @throws InvalidSchemaException
     */
    public function __construct(
        Node ...$node,
    ) {
        $this->configSchema = [] === $node ? null : new ConfigSchema(...$node);
    }

    #[\Override]
    public function validatePrototype(array $array): void
    {
        foreach ($array as $item) {
            if (!is_array($item)) {
                throw new InvalidNodeTypeException();
            }

            $this->validateObject($item);
        }
    }

    /**
     * @param array<int, array<string, mixed>> $array
     *
     * @return array<int, array<string, mixed>>
     */
    #[\Override]
    public function processPrototype(array $array): array
    {
        foreach ($array as $key => $value) {
            $array[$key] = $this->processPrototypeValue($value);
        }

        return $array;
    }

    /**
     * @param array<string, mixed> $array
     *
     * @return array<string, mixed>
     */
    private function processPrototypeValue(array $array): array
    {
        $this->appendDefaultValueForMissingNodes($array);

        if (null === $this->configSchema) {
            return $array;
        }

        foreach ($array as $key => $value) {
            $node = $this->configSchema->getNode($key);

            if (null === $node) {
                throw new \RuntimeException(sprintf(
                    'Node "%s" is not configured for your config',
                    $key
                ));
            }

            $array[$key] = $node->processValue($value);
        }

        return $array;
    }

    /**
     * @param array<string, mixed> $object
     *
     * @throws InvalidNodeTypeException
     */
    private function validateObject(array $object): void
    {
        if ([] === $object) {
            throw new InvalidNodeTypeException('Empty object are not allowed');
        }

        foreach ($object as $key => $value) {
            if (!is_string($key)) {
                throw new InvalidNodeTypeException('must be of type object');
            }

            if (null === $this->configSchema) {
                continue;
            }

            $node = $this->configSchema->getNode($key);

            if (null === $node) {
                throw new InvalidNodeTypeException(sprintf(
                    'key %s not allowed by the prototype, expected keys "%s"',
                    $key,
                    implode('", "', $this->configSchema->getNodeKeys())
                ));
            }

            $node->validateType($value);
        }
    }

    /**
     * @param array<string, mixed> $config
     */
    private function appendDefaultValueForMissingNodes(array &$config): void
    {
        if (null === $this->configSchema) {
            return;
        }

        foreach ($this->configSchema->getNodes() as $node) {
            if (!array_key_exists($node->getKey(), $config) && null !== $defaultValue = $node->getDefaultValue()) {
                $config[$node->getKey()] = $defaultValue;
            }
        }
    }
}
