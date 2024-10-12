<?php

namespace Kaizen\Components\Config\Schema\Prototype;

use Kaizen\Components\Config\Exception\InvalidNodeTypeException;

class StringPrototype extends AbstractPrototype
{
    #[\Override]
    public function validatePrototype(array $array): void
    {
        foreach ($array as $item) {
            if (!is_string($item)) {
                throw new InvalidNodeTypeException('Invalid prototype the array must contains only strings.');
            }
        }
    }
}
