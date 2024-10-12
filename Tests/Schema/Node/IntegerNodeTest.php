<?php

namespace Kaizen\Components\Config\Tests\Schema\Node;

use Kaizen\Components\Config\Exception\ConfigProcessingException;
use Kaizen\Components\Config\Exception\InvalidNodeTypeException;
use Kaizen\Components\Config\Schema\Node\IntegerNode;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(IntegerNode::class)]
class IntegerNodeTest extends TestCase
{
    public function testValidateType(): void
    {
        $integerNode = new IntegerNode('integer');
        $integerNode->validateType(123);

        $this->assertSame('integer', $integerNode->getKey());
    }

    public function testProcessValue(): void
    {
        $integerNode = new IntegerNode('int');
        $integerNode->min(10);

        self::expectException(ConfigProcessingException::class);
        $integerNode->processValue(1);
    }

    public static function invalidValueProvider(): \Iterator
    {
        yield [true];

        yield ['test'];

        yield [123.123];

        yield [[]];

        yield [new \stdClass()];
    }

    #[DataProvider('invalidValueProvider')]
    public function testException(mixed $value): void
    {
        $integerNode = new IntegerNode('integer');

        $this->expectException(InvalidNodeTypeException::class);
        $integerNode->validateType($value);
    }
}
