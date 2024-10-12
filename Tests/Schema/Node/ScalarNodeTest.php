<?php

namespace Kaizen\Components\Config\Tests\Schema\Node;

use Kaizen\Components\Config\Exception\InvalidNodeTypeException;
use Kaizen\Components\Config\Schema\Node\ScalarNode;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(ScalarNode::class)]
class ScalarNodeTest extends TestCase
{
    public static function validValueProvider(): \Iterator
    {
        yield ['string'];

        yield [123];

        yield [true];

        yield [123.123];
    }

    #[DataProvider('validValueProvider')]
    public function testValidateType(): void
    {
        $scalarNode = new ScalarNode('scalar');
        $scalarNode->validateType(true);

        $this->assertSame('scalar', $scalarNode->getKey());
    }

    public static function invalidValueProvider(): \Iterator
    {
        yield [[]];

        yield [new \stdClass()];
    }

    #[DataProvider('invalidValueProvider')]
    public function testException(mixed $value): void
    {
        $scalarNode = new ScalarNode('scalar');

        $this->expectException(InvalidNodeTypeException::class);
        $scalarNode->validateType($value);
    }
}
