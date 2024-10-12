<?php

declare(strict_types=1);

namespace Kaizen\Components\Config\Tests\Schema;

use Kaizen\Components\Config\Schema\ConfigSchema;
use Kaizen\Components\Config\Schema\ConfigSchemaBuilder;
use Kaizen\Components\Config\Schema\Node\BooleanNode;
use Kaizen\Components\Config\Schema\Node\FloatNode;
use Kaizen\Components\Config\Schema\Node\IntegerNode;
use Kaizen\Components\Config\Schema\Node\NodeInterface;
use Kaizen\Components\Config\Schema\Node\ObjectNode;
use Kaizen\Components\Config\Schema\Node\ScalarNode;
use Kaizen\Components\Config\Schema\Node\StringNode;
use Kaizen\Components\Config\Schema\Prototype\ScalarPrototype;
use Kaizen\Components\Config\Schema\Prototype\StringPrototype;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(ConfigSchemaBuilder::class)]
class ConfigSchemaBuilderTest extends TestCase
{
    public function testBuild(): void
    {
        $configSchemaBuilder = new ConfigSchemaBuilder();

        $configSchema = $configSchemaBuilder
            ->integer('int')
            ->min(1)
            ->max(100)
            ->required()
            ->buildNode()
            ->float('float')
            ->min(0.1)
            ->max(10.20)
            ->defaultValue(0.1)
            ->buildNode()
            ->string('string')
            ->required()
            ->defaultValue('default_string')
            ->buildNode()
            ->child('object')
            ->scalar('scalar')
            ->defaultValue('default_scalar')
            ->buildNode()
            ->boolean('boolean')->buildNode()
            ->buildChildNode()
            ->objectVariable('objectVariable')
            ->withPrototype(new StringPrototype())
            ->buildNode()
            ->array('array')
            ->withPrototype(new ScalarPrototype())
            ->buildNode()
            ->build()
        ;

        $this->assertInstanceOf(ConfigSchema::class, $configSchema);

        $integerNode = $configSchema->getNode('int');
        $this->assertInstanceOf(IntegerNode::class, $integerNode);
        $this->assertSame(1, $integerNode->getMin());

        $floatNode = $configSchema->getNode('float');
        $this->assertInstanceOf(FloatNode::class, $floatNode);
        $this->assertEqualsWithDelta(0.1, $floatNode->getMin(), PHP_FLOAT_EPSILON);
        $this->assertEqualsWithDelta(10.20, $floatNode->getMax(), PHP_FLOAT_EPSILON);

        $stringNode = $configSchema->getNode('string');
        $this->assertInstanceOf(StringNode::class, $stringNode);
        $this->assertTrue($stringNode->isRequired());
        $this->assertSame('default_string', $stringNode->getDefaultValue());

        $objectNode = $configSchema->getNode('object');
        $this->assertInstanceOf(ObjectNode::class, $objectNode);
        $childClasses = array_map(
            static fn (NodeInterface $node): string => $node::class,
            $objectNode->getChildren()->getNodes()
        );
        $this->assertSame([ScalarNode::class, BooleanNode::class], $childClasses);
    }
}
