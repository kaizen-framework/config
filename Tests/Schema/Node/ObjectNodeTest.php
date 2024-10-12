<?php

declare(strict_types=1);

namespace Kaizen\Components\Config\Tests\Schema\Node;

use Kaizen\Components\Config\Exception\InvalidNodeTypeException;
use Kaizen\Components\Config\Schema\ConfigSchema;
use Kaizen\Components\Config\Schema\Node\ArrayNode;
use Kaizen\Components\Config\Schema\Node\IntegerNode;
use Kaizen\Components\Config\Schema\Node\ObjectNode;
use Kaizen\Components\Config\Schema\Node\StringNode;
use Kaizen\Components\Config\Schema\Prototype\ObjectPrototype;
use Kaizen\Components\Config\Schema\Prototype\TuplePrototype;
use Kaizen\Components\Config\Schema\Prototype\TupleTypesEnum;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(ObjectNode::class)]
class ObjectNodeTest extends TestCase
{
    public function testValidateType(): void
    {
        $objectNode = new ObjectNode(
            'object',
            new ConfigSchema(
                new ArrayNode('tuple_array', new TuplePrototype(
                    TupleTypesEnum::SCALAR,
                    TupleTypesEnum::INTEGER,
                    TupleTypesEnum::BOOLEAN
                )),
                new ArrayNode('object_array', new ObjectPrototype(
                    new StringNode('sub_object_array')
                )),
                new StringNode('string'),
                new IntegerNode('int'),
            )
        );

        $objectNode->validateType([
            'tuple_array' => ['string', 123, true],
            'object_array' => [
                ['sub_object_array' => 'string'],
            ],
            'string' => 'string',
            'int' => 123,
        ]);

        $this->assertSame('object', $objectNode->getKey());
    }

    public static function invalidValueProvider(): \Iterator
    {
        yield [[
            'tuple_array' => ['string', true],
            'object_array' => [
                ['sub_object_array' => 'string'],
            ],
            'string' => 'string',
            'int' => 123,
        ]];

        yield [[
            'tuple_array' => ['string', 123, true],
            'object_array' => [
                ['sub_object_array_invalid_name' => 'string'],
            ],
            'string' => 'string',
            'int' => 123,
        ]];

        yield [[
            'tuple_array' => ['string', 123, true],
            'object_array' => [
                ['sub_object_array' => 'string'],
            ],
            'string' => true,
            'int' => 123,
        ]];

        yield [[
            'tuple_array' => ['string', 123, true],
            'object_array' => [
                ['sub_object_array' => 'string'],
            ],
            'string' => 'string',
            'int' => 'other string',
        ]];

        yield [[
            'invalid_key' => ['string', 123, true],
            'object_array' => [
                ['sub_object_array' => 'string'],
            ],
            'string' => 'string',
            'int' => 123,
        ]];
    }

    /**
     * @param array<string, mixed> $value
     */
    #[DataProvider('invalidValueProvider')]
    public function testException(array $value): void
    {
        $objectNode = new ObjectNode('object', new ConfigSchema(
            new ArrayNode('tuple_array', new TuplePrototype(
                TupleTypesEnum::SCALAR,
                TupleTypesEnum::INTEGER,
                TupleTypesEnum::BOOLEAN
            )),
            new ArrayNode('object_array', new ObjectPrototype(
                new StringNode('sub_object_array')
            )),
            new StringNode('string'),
            new IntegerNode('int'),
        ));

        self::expectException(InvalidNodeTypeException::class);
        $objectNode->validateType($value);
    }
}
