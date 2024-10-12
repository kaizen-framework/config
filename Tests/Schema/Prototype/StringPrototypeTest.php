<?php

namespace Kaizen\Components\Config\Tests\Schema\Prototype;

use Kaizen\Components\Config\Exception\InvalidNodeTypeException;
use Kaizen\Components\Config\Schema\Node\ArrayNode;
use Kaizen\Components\Config\Schema\Prototype\StringPrototype;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(StringPrototype::class)]
class StringPrototypeTest extends TestCase
{
    public function testValidateArray(): void
    {
        self::expectNotToPerformAssertions();

        $stringPrototype = new StringPrototype();

        $stringPrototype->validatePrototype(['test', 'test2', 'test3']);
    }

    public function testValidateWithArrayNode(): void
    {
        self::expectNotToPerformAssertions();

        $arrayNode = new ArrayNode('string', new StringPrototype());

        $arrayNode->validateType(['test', 'test2', 'test3']);
    }

    public static function invalidValuesProvider(): \Iterator
    {
        yield [[123]];

        yield [['test', 123]];

        yield [['test', 123.123, false]];

        yield [[123, 'test', true]];

        yield [[true, 'test', true]];

        yield [['test', 123, true, 'other']];
    }

    /**
     * @param array<int, mixed> $value
     */
    #[DataProvider('invalidValuesProvider')]
    public function testWithInvalidArray(array $value): void
    {
        $stringPrototype = new StringPrototype();

        self::expectException(InvalidNodeTypeException::class);
        $stringPrototype->validatePrototype($value);
    }

    /**
     * @param array<int, mixed> $value
     */
    #[DataProvider('invalidValuesProvider')]
    public function testInvalidWithArrayNode(array $value): void
    {
        $arrayNode = new ArrayNode('array_node', new StringPrototype());

        self::expectException(InvalidNodeTypeException::class);
        $arrayNode->validateType($value);
    }
}
