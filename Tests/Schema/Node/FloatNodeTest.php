<?php

namespace Kaizen\Components\Config\Tests\Schema\Node;

use Kaizen\Components\Config\Exception\ConfigProcessingException;
use Kaizen\Components\Config\Exception\InvalidNodeTypeException;
use Kaizen\Components\Config\Schema\Node\FloatNode;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(FloatNode::class)]
class FloatNodeTest extends TestCase
{
    public function testValidateType(): void
    {
        $floatNode = new FloatNode('float');
        $floatNode->validateType(12.3);

        $this->assertSame('float', $floatNode->getKey());
    }

    public function testProcessValue(): void
    {
        $floatNode = new FloatNode('float');
        $floatNode->min(10.5);

        self::expectException(ConfigProcessingException::class);
        $floatNode->processValue(1.3);
    }

    public static function invalidValueProvider(): \Iterator
    {
        yield [true];

        yield ['test'];

        yield [123];

        yield [[]];

        yield [new \stdClass()];
    }

    #[DataProvider('invalidValueProvider')]
    public function testException(mixed $value): void
    {
        $floatNode = new FloatNode('float');

        $this->expectException(InvalidNodeTypeException::class);
        $floatNode->validateType($value);
    }
}
