<?php

namespace Kaizen\Components\Config\Tests\Schema\Node;

use Kaizen\Components\Config\Exception\InvalidNodeTypeException;
use Kaizen\Components\Config\Schema\Node\BooleanNode;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(BooleanNode::class)]
class BooleanNodeTest extends TestCase
{
    public function testValidateType(): void
    {
        $booleanNode = new BooleanNode('boolean');
        $booleanNode->validateType(true);

        $this->assertSame('boolean', $booleanNode->getKey());
    }

    public static function invalidValueProvider(): \Iterator
    {
        yield [123];

        yield ['test'];

        yield [123.123];

        yield [[]];

        yield [new \stdClass()];
    }

    #[DataProvider('invalidValueProvider')]
    public function testException(mixed $value): void
    {
        $booleanNode = new BooleanNode('boolean');

        $this->expectException(InvalidNodeTypeException::class);
        $booleanNode->validateType($value);
    }
}
