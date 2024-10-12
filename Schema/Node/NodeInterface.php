<?php

namespace Kaizen\Components\Config\Schema\Node;

use Kaizen\Components\Config\Exception\ConfigProcessingException;
use Kaizen\Components\Config\Exception\InvalidNodeTypeException;

interface NodeInterface
{
    /**
     * Validate the type of the node, throw an exception if the type not matching the expected one.
     *
     * @throws InvalidNodeTypeException
     */
    public function validateType(mixed $value): void;

    /**
     * Get the node key.
     */
    public function getKey(): string;

    /**
     * @throws ConfigProcessingException
     */
    public function processValue(mixed $value): mixed;
}
