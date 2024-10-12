<?php

namespace Kaizen\Components\Config\Tests\Schema\Prototype;

use Kaizen\Components\Config\Exception\InvalidNodeTypeException;
use Kaizen\Components\Config\Schema\Node\ArrayNode;
use Kaizen\Components\Config\Schema\Prototype\ScalarPrototype;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(ScalarPrototype::class)]
class ScalarPrototypeTest extends TestCase
{
    public function testValidateArray(): void
    {
        self::expectNotToPerformAssertions();

        $scalarPrototype = new ScalarPrototype();
        $scalarPrototype->validatePrototype([123, 12.3, true, 'string']);
    }

    public function testValidateWithArrayNode(): void
    {
        self::expectNotToPerformAssertions();

        $arrayNode = new ArrayNode('node', new ScalarPrototype());
        $arrayNode->validateType([123, 12.3, true, 'string']);
    }

    public static function invalidScalarArrayValueProvider(): \Iterator
    {
        yield [[[]]];

        yield [[new \stdClass()]];

        yield [[null]];
    }

    /**
     * @param array<int, mixed> $value
     */
    #[DataProvider('invalidScalarArrayValueProvider')]
    public function testScalarException(array $value): void
    {
        $scalarPrototype = new ScalarPrototype();

        self::expectException(InvalidNodeTypeException::class);
        $scalarPrototype->validatePrototype($value);
    }

    /**
     * @param array<int, mixed> $value
     */
    #[DataProvider('invalidScalarArrayValueProvider')]
    public function testScalarExceptionWithArrayNode(array $value): void
    {
        $scalarArrayNode = new ArrayNode('node', new ScalarPrototype());

        self::expectException(InvalidNodeTypeException::class);
        $scalarArrayNode->validateType($value);
    }
}
