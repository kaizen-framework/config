<?php

declare(strict_types=1);

namespace Kaizen\Components\Config\Processor;

use Kaizen\Components\Config\ConfigInterface;
use Kaizen\Components\Config\Exception\ConfigProcessingException;
use Kaizen\Components\Config\Exception\InvalidNodeTypeException;
use Kaizen\Components\Config\Exception\NodeNotFoundException;
use Kaizen\Components\Config\Schema\ConfigSchema;
use Kaizen\Components\Config\Schema\Node\NodeInterface;
use Kaizen\Components\Config\Schema\Node\ParentNodeInterface;

class ConfigProcessor
{
    /**
     * @param array<string, mixed> $config
     *
     * @throws InvalidNodeTypeException
     * @throws NodeNotFoundException
     * @throws ConfigProcessingException
     */
    public function processConfig(array &$config, ConfigInterface $configInterface): void
    {
        $configSchema = $configInterface->getConfigSchema();

        $config = $this->doProcess($config, $configSchema);
    }

    /**
     * @param array<string, mixed> $config
     *
     * @return array<string, mixed>
     *
     * @throws InvalidNodeTypeException
     * @throws NodeNotFoundException
     * @throws ConfigProcessingException
     */
    private function doProcess(array $config, ConfigSchema $configSchema): array
    {
        $this->assertRequiredNodeArePresent($configSchema, $config);
        $this->appendDefaultValueForMissingNodes($config, $configSchema);

        foreach ($config as $key => $value) {
            $node = $configSchema->getNode($key);

            if (!$node instanceof NodeInterface) {
                throw new NodeNotFoundException($key);
            }

            if ($node instanceof ParentNodeInterface) {
                $config[$key] = $this->doProcess($value, $node->getChildren());

                continue;
            }

            $config[$key] = $node->processValue($value);
            $node->validateType($value);
        }

        return $config;
    }

    /**
     * @param array<string, mixed> $config
     */
    private function appendDefaultValueForMissingNodes(array &$config, ConfigSchema $configSchema): void
    {
        foreach ($configSchema->getNodes() as $node) {
            if (!array_key_exists($node->getKey(), $config) && null !== $defaultValue = $node->getDefaultValue()) {
                $config[$node->getKey()] = $defaultValue;
            }
        }
    }

    /**
     * @param array<string, mixed> $config
     *
     * @throws ConfigProcessingException
     */
    private function assertRequiredNodeArePresent(ConfigSchema $configSchema, array $config): void
    {
        foreach ($configSchema->getNodes() as $node) {
            if ($node->isRequired() && !array_key_exists($node->getKey(), $config)) {
                throw new ConfigProcessingException(sprintf(
                    'Required config parameter "%s" is missing.',
                    $node->getKey()
                ));
            }
        }
    }
}
