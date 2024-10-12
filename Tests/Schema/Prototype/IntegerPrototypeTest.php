<?php

namespace Kaizen\Components\Config\Tests\Schema\Prototype;

use Kaizen\Components\Config\Exception\InvalidNodeTypeException;
use Kaizen\Components\Config\Schema\Node\ArrayNode;
use Kaizen\Components\Config\Schema\Prototype\IntegerPrototype;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(IntegerPrototype::class)]
class IntegerPrototypeTest extends TestCase
{
    public function testValidateArray(): void
    {
        self::expectNotToPerformAssertions();

        $integerPrototype = new IntegerPrototype();
        $integerPrototype->validatePrototype([123, 12]);
    }

    public function testValidateWithArrayNode(): void
    {
        self::expectNotToPerformAssertions();

        $arrayNode = new ArrayNode('node', new IntegerPrototype());
        $arrayNode->validateType([123, 12]);
    }

    public static function invalidIntegerArrayValueProvider(): \Iterator
    {
        yield [[12.12]];

        yield [['string']];

        yield [[true]];

        yield [[true, 'test']];
    }

    /**
     * @param array<int, mixed> $value
     */
    #[DataProvider('invalidIntegerArrayValueProvider')]
    public function testIntegerException(array $value): void
    {
        $integerPrototype = new IntegerPrototype();

        self::expectException(InvalidNodeTypeException::class);
        $integerPrototype->validatePrototype($value);
    }

    #[DataProvider('invalidIntegerArrayValueProvider')]
    public function testIntegerExceptionWithArrayNode(mixed $value): void
    {
        $scalarArrayNode = new ArrayNode('node', new IntegerPrototype());

        self::expectException(InvalidNodeTypeException::class);
        $scalarArrayNode->validateType($value);
    }
}
