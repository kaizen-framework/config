<?php

namespace Kaizen\Components\Config\Tests\Schema\Node;

use Kaizen\Components\Config\Exception\InvalidNodeTypeException;
use Kaizen\Components\Config\Schema\Node\ArrayNode;
use Kaizen\Components\Config\Schema\Prototype\ConfigPrototypeInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(ArrayNode::class)]
class ArrayNodeTest extends TestCase
{
    public function testValidateType(): void
    {
        $arrayNode = new ArrayNode('array');

        $arrayNode->validateType(['string', 123, true]);

        $this->assertSame('array', $arrayNode->getKey());
    }

    public static function invalidValuesProvider(): \Iterator
    {
        yield [123];

        yield [12.3];

        yield ['string'];

        yield [true];
    }

    #[DataProvider('invalidValuesProvider')]
    public function testWithInvalidValue(mixed $value): void
    {
        $arrayNode = new ArrayNode('array');

        $this->expectException(InvalidNodeTypeException::class);
        $arrayNode->validateType($value);
    }

    public function testPrototypeValidateIsCalled(): void
    {
        $value = [
            'parameter' => 'value',
        ];

        $prototypeMock = $this->createMock(ConfigPrototypeInterface::class);
        $prototypeMock->expects(self::once())
            ->method('validatePrototype')
            ->with($value)
        ;

        $arrayNode = new ArrayNode('array', $prototypeMock);
        $arrayNode->validateType($value);
    }
}
