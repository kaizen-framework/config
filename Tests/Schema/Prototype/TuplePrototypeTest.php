<?php

namespace Kaizen\Components\Config\Tests\Schema\Prototype;

use Kaizen\Components\Config\Exception\InvalidNodeTypeException;
use Kaizen\Components\Config\Schema\Node\ArrayNode;
use Kaizen\Components\Config\Schema\Prototype\TuplePrototype;
use Kaizen\Components\Config\Schema\Prototype\TupleTypesEnum;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(TuplePrototype::class)]
class TuplePrototypeTest extends TestCase
{
    public function testValidateArray(): void
    {
        self::expectNotToPerformAssertions();

        $tuplePrototype = new TuplePrototype(
            TupleTypesEnum::STRING,
            TupleTypesEnum::INTEGER,
            TupleTypesEnum::BOOLEAN,
            TupleTypesEnum::FLOAT,
            TupleTypesEnum::SCALAR
        );

        $tuplePrototype->validatePrototype(['string', 123, true, 12.12, 'scalar']);
    }

    public function testValidateWithArrayNode(): void
    {
        self::expectNotToPerformAssertions();

        $arrayNode = new ArrayNode('tuple', new TuplePrototype(
            TupleTypesEnum::STRING,
            TupleTypesEnum::INTEGER,
            TupleTypesEnum::BOOLEAN
        ));

        $arrayNode->validateType(['string', 123, true]);
    }

    public static function invalidTupleValueProvider(): \Iterator
    {
        yield [[123]];

        yield [['test', 123]];

        yield [['test', 123.123, false]];

        yield [[123, 'test', true]];

        yield [[true, 'test', true]];

        yield [[12, 23.32, true]];

        yield [['test', 123, true, 'other']];
    }

    /**
     * @param array<int, mixed> $value
     */
    #[DataProvider('invalidTupleValueProvider')]
    public function testTupleException(array $value): void
    {
        $tuplePrototype = new TuplePrototype(
            TupleTypesEnum::FLOAT,
            TupleTypesEnum::INTEGER,
            TupleTypesEnum::BOOLEAN
        );

        self::expectException(InvalidNodeTypeException::class);
        $tuplePrototype->validatePrototype($value);
    }

    #[DataProvider('invalidTupleValueProvider')]
    public function testTupleExceptionWithArrayNode(mixed $value): void
    {
        $arrayNode = new ArrayNode('tuple', new TuplePrototype(
            TupleTypesEnum::STRING,
            TupleTypesEnum::INTEGER,
            TupleTypesEnum::BOOLEAN
        ));

        self::expectException(InvalidNodeTypeException::class);
        $arrayNode->validateType($value);
    }
}
