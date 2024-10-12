<?php

namespace Kaizen\Components\Config\Tests\Schema;

use Kaizen\Components\Config\Exception\InvalidSchemaException;
use Kaizen\Components\Config\Schema\ConfigSchema;
use Kaizen\Components\Config\Schema\Node\ArrayNode;
use Kaizen\Components\Config\Schema\Node\BooleanNode;
use Kaizen\Components\Config\Schema\Node\IntegerNode;
use Kaizen\Components\Config\Schema\Node\ObjectNode;
use Kaizen\Components\Config\Schema\Node\StringNode;
use Kaizen\Components\Config\Schema\Prototype\ObjectPrototype;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(ConfigSchema::class)]
class ConfigSchemaTest extends TestCase
{
    public function testGetNode(): void
    {
        $configSchema = new ConfigSchema(
            new BooleanNode('is_test'),
            new StringNode('test_string'),
            new ObjectNode('parameters', new ConfigSchema(
                new StringNode('parameter1'),
                new BooleanNode('parameter2'),
                new ObjectNode('parameter3', new ConfigSchema(
                    new StringNode('parameter4'),
                    new ArrayNode('parameter5', new ObjectPrototype(
                        new StringNode('parameter6'),
                        new IntegerNode('parameter7')
                    )),
                )),
            )),
        );

        $node = $configSchema->getNode('parameters');

        $this->assertNotNull($node);
        $this->assertSame('parameters', $node->getKey());
    }

    public function testExceptionOnDuplicatedKeys(): void
    {
        $this->expectException(InvalidSchemaException::class);

        new ConfigSchema(
            new BooleanNode('duplicate'),
            new StringNode('duplicate'),
            new ObjectNode('parameters', new ConfigSchema(
                new StringNode('parameter1'),
                new BooleanNode('parameter2'),
                new ObjectNode('parameter3', new ConfigSchema(
                    new StringNode('parameter4'),
                    new ArrayNode('parameter5', new ObjectPrototype(
                        new StringNode('parameter6'),
                        new IntegerNode('parameter7')
                    )),
                )),
            )),
        );
    }
}
