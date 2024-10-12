<?php

namespace Kaizen\Components\Config\Schema\Prototype;

use Kaizen\Components\Config\Exception\InvalidNodeTypeException;

class ScalarPrototype extends AbstractPrototype
{
    #[\Override]
    public function validatePrototype(array $array): void
    {
        foreach ($array as $item) {
            if (!is_scalar($item)) {
                throw new InvalidNodeTypeException('Invalid value the array must contains only scalar values.');
            }
        }
    }
}
