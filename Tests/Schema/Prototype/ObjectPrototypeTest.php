<?php

namespace Kaizen\Components\Config\Tests\Schema\Prototype;

use Kaizen\Components\Config\Exception\InvalidNodeTypeException;
use Kaizen\Components\Config\Schema\Node\BooleanNode;
use Kaizen\Components\Config\Schema\Node\IntegerNode;
use Kaizen\Components\Config\Schema\Node\ScalarNode;
use Kaizen\Components\Config\Schema\Node\StringNode;
use Kaizen\Components\Config\Schema\Prototype\ObjectPrototype;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(ObjectPrototype::class)]
class ObjectPrototypeTest extends TestCase
{
    public function testValidate(): void
    {
        self::expectNotToPerformAssertions();

        $objectPrototype = new ObjectPrototype();
        $objectPrototype->validatePrototype([
            [
                'node1' => 'string',
                'node2' => 233,
                'node3' => true,
                'node4' => ['ok', 123, true],
                'node5' => ['key1' => 'string'],
            ],
            [
                'node6' => 'string',
                'node7' => 233,
                'node8' => true,
            ],
        ]);
    }

    public function testValidateWithDefinedEntries(): void
    {
        $this->expectNotToPerformAssertions();

        $objectPrototype = new ObjectPrototype(
            new ScalarNode('node1'),
            new StringNode('node2'),
            new BooleanNode('node3'),
            new IntegerNode('node4')
        );
        $objectPrototype->validatePrototype([
            ['node1' => true, 'node2' => 'string', 'node3' => true, 'node4' => 123],
            ['node1' => 123, 'node2' => 'string2', 'node3' => true, 'node4' => 321],
            ['node1' => 'string', 'node2' => 'string3', 'node3' => true, 'node4' => 231],
        ]);
    }

    public static function invalidValueProvider(): \Iterator
    {
        yield [[
            ['node1' => true, 'node2' => 'string', 'node3' => true, 'node4' => 123],
            [],
        ]];

        yield [[
            ['node1' => true, 'node2' => 'string', 'node3' => true, 'node4' => 123],
            ['node1' => 123, 'node2' => true, 'node3' => true, 'node4' => 123],
        ]];

        yield [[
            [],
            ['node1' => true, 'node2' => 'string', 'node3' => true, 'node4' => 123],
        ]];

        yield [[
            'string',
            ['node1' => true, 'node2' => 'string', 'node3' => true, 'node4' => 123],
        ]];

        yield [[
            ['node1' => true, 'node2' => 'string', 'node32' => true, 'node4' => 123],
            ['node1' => true, 'node2' => 'string', 'node3' => true, 'node4' => 123],
        ]];

        yield [[
            ['node1' => true, 'node2' => 'string', 'node3' => 123, 'node4' => 'string'],
            ['node1' => true, 'node2' => 'string', 'node3' => true, 'node4' => 123],
            ['node1' => true, 'node2' => 123, 'node3' => 'string', 'node45' => 123],
        ]];
    }

    /**
     * @param array<int, array<string, mixed>> $value
     */
    #[DataProvider('invalidValueProvider')]
    public function testWithInvalidValue(array $value): void
    {
        $objectPrototype = new ObjectPrototype(
            new ScalarNode('node1'),
            new StringNode('node2'),
            new BooleanNode('node3'),
            new IntegerNode('node4')
        );

        self::expectException(InvalidNodeTypeException::class);

        $objectPrototype->validatePrototype($value);
    }
}
