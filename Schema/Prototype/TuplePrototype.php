<?php

namespace Kaizen\Components\Config\Schema\Prototype;

use Kaizen\Components\Config\Exception\InvalidNodeTypeException;

class TuplePrototype extends AbstractPrototype
{
    /** @var TupleTypesEnum[] */
    private array $types;

    public function __construct(
        TupleTypesEnum ...$tupleTypesEnum
    ) {
        $this->types = $tupleTypesEnum;
    }

    /**
     * @param array<int, mixed> $array
     *
     * @throws InvalidNodeTypeException
     */
    #[\Override]
    public function validatePrototype(array $array): void
    {
        if (count($array) !== count($this->types)) {
            throw new InvalidNodeTypeException();
        }

        foreach ($array as $key => $value) {
            $expectedType = $this->types[$key];

            if (
                (TupleTypesEnum::SCALAR === $expectedType && !is_scalar($value))
                || (TupleTypesEnum::SCALAR !== $expectedType && gettype($value) !== $expectedType->value)
            ) {
                throw new InvalidNodeTypeException();
            }
        }
    }
}
