<?php

declare(strict_types=1);

namespace Kaizen\Components\Config\Schema\Prototype;

abstract class AbstractPrototype implements ConfigPrototypeInterface
{
    #[\Override]
    public function processPrototype(array $array): array
    {
        return $array;
    }
}
