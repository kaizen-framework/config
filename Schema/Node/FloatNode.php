<?php

declare(strict_types=1);

namespace Kaizen\Components\Config\Schema\Node;

use Kaizen\Components\Config\Exception\InvalidNodeTypeException;

class FloatNode extends NumericNode
{
    #[\Override]
    public function validateType(mixed $value): void
    {
        if (!is_float($value)) {
            throw new InvalidNodeTypeException(sprintf(
                'The node "%s" must be of type "float", "%s" given',
                $this->getKey(),
                get_debug_type($value)
            ));
        }
    }
}
