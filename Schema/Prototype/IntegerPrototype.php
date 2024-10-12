<?php

declare(strict_types=1);

namespace Kaizen\Components\Config\Schema\Prototype;

use Kaizen\Components\Config\Exception\InvalidNodeTypeException;

class IntegerPrototype extends AbstractPrototype
{
    /**
     * @param array<int, mixed> $array
     *
     * @throws InvalidNodeTypeException
     */
    #[\Override]
    public function validatePrototype(array $array): void
    {
        foreach ($array as $item) {
            if (!is_int($item)) {
                throw new InvalidNodeTypeException(sprintf(
                    'Type %s not allowed, expected : int',
                    gettype($item)
                ));
            }
        }
    }
}
