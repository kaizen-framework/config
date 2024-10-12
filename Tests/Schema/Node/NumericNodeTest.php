<?php

namespace Kaizen\Components\Config\Tests\Schema\Node;

use Kaizen\Components\Config\Exception\ConfigProcessingException;
use Kaizen\Components\Config\Exception\InvalidNodeTypeException;
use Kaizen\Components\Config\Schema\Node\NumericNode;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(NumericNode::class)]
class NumericNodeTest extends TestCase
{
    public function testValidateType(): void
    {
        $numericNode = new NumericNode('numeric');

        $numericNode->validateType(12.3);
        $numericNode->validateType(12);

        $this->assertSame('numeric', $numericNode->getKey());
    }

    public static function invalidNumericValueProvider(): \Iterator
    {
        yield 'With int value under the min' => [
            2, 10, 1,
        ];

        yield 'With float value under the min' => [
            2.5, 10.5, 1.5,
        ];

        yield 'With int value over the min' => [
            10, 105, 150,
        ];

        yield 'With float value over the min' => [
            2.5, 10.5, 19.5,
        ];
    }

    #[DataProvider('invalidNumericValueProvider')]
    public function testProcessValue(float|int $min, float|int $max, float|int $actualValue): void
    {
        $numericNode = new NumericNode('numeric');
        $numericNode->min($min)->max($max);

        self::expectException(ConfigProcessingException::class);

        $numericNode->processValue($actualValue);
    }

    public static function invalidValueProvider(): \Iterator
    {
        yield [true];

        yield ['test'];

        yield [[]];

        yield [new \stdClass()];
    }

    #[DataProvider('invalidValueProvider')]
    public function testException(mixed $value): void
    {
        $numericNode = new NumericNode('numeric');

        $this->expectException(InvalidNodeTypeException::class);
        $numericNode->validateType($value);
    }
}
