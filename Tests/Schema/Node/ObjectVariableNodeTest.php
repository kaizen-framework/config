<?php

declare(strict_types=1);

namespace Kaizen\Components\Config\Tests\Schema\Node;

use Kaizen\Components\Config\Exception\InvalidNodeTypeException;
use Kaizen\Components\Config\Schema\Node\ObjectVariableNode;
use Kaizen\Components\Config\Schema\Prototype\ConfigPrototypeInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(ObjectVariableNode::class)]
class ObjectVariableNodeTest extends TestCase
{
    public function testValidate(): void
    {
        self::expectNotToPerformAssertions();
        $objectVariableNode = new ObjectVariableNode('node');

        $objectVariableNode->validateType([
            'variable' => 'test',
            'int' => 123,
            'bool' => true,
            'parameters' => [
                'parameter1' => 'test',
                'parameter2' => 'test2',
            ],
        ]);
    }

    public function testPrototypeValidationIsCalled(): void
    {
        $value = [
            'variable' => 'test',
            'parameters' => [
                'parameter1' => 'test',
                'parameter2' => 'test2',
            ],
        ];

        $prototypeMock = $this->createMock(ConfigPrototypeInterface::class);
        $prototypeMock->expects(self::once())
            ->method('validatePrototype')
            ->with($value)
        ;

        $objectVariableNode = new ObjectVariableNode('node', $prototypeMock);

        $objectVariableNode->validateType($value);
    }

    public function testWithNotObjectValue(): void
    {
        $objectVariableNode = new ObjectVariableNode('node');

        self::expectException(InvalidNodeTypeException::class);
        $objectVariableNode->validateType('invalid');
    }

    public function testWithObjectWithoutKey(): void
    {
        $objectVariableNode = new ObjectVariableNode('node');

        self::expectException(InvalidNodeTypeException::class);
        $objectVariableNode->validateType([
            'variable' => 'test',
            'no keys',
            'bool' => true,
            'parameters' => [
                'parameter1' => 'test',
                'parameter2' => 'test2',
            ],
        ]);
    }
}
