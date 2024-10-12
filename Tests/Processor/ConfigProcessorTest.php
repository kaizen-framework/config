<?php

declare(strict_types=1);

namespace Kaizen\Components\Config\Tests\Processor;

use Kaizen\Components\Config\ConfigInterface;
use Kaizen\Components\Config\Exception\ConfigProcessingException;
use Kaizen\Components\Config\Exception\InvalidNodeTypeException;
use Kaizen\Components\Config\Exception\NodeNotFoundException;
use Kaizen\Components\Config\Processor\ConfigProcessor;
use Kaizen\Components\Config\Schema\ConfigSchema;
use Kaizen\Components\Config\Schema\Node\BooleanNode;
use Kaizen\Components\Config\Schema\Node\ObjectNode;
use Kaizen\Components\Config\Schema\Node\ObjectVariableNode;
use Kaizen\Components\Config\Schema\Node\ScalarNode;
use Kaizen\Components\Config\Schema\Node\StringNode;
use Kaizen\Components\Config\Schema\Prototype\StringPrototype;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(ConfigProcessor::class)]
class ConfigProcessorTest extends TestCase
{
    public function testDefaultValuesAreAppendForMissingConfig(): void
    {
        $config = [
            'node' => 'test',
        ];

        $configSchema = new ConfigSchema(
            (new StringNode('node'))->defaultValue('default'),
            (new ScalarNode('node2'))->defaultValue('default_value')
        );

        $configInterface = $this->createMock(ConfigInterface::class);
        $configInterface->expects($this->once())
            ->method('getConfigSchema')
            ->willReturn($configSchema)
        ;

        $configProcessor = new ConfigProcessor();

        $configProcessor->processConfig($config, $configInterface);

        $this->assertSame('test', $config['node']);
        $this->assertSame('default_value', $config['node2']);
    }

    public function testNodeProcessValueIsCalled(): void
    {
        $configSchema = $this->createMock(ConfigSchema::class);

        $node = $this->createMock(StringNode::class);
        $node->method('getKey')->willReturn('node');
        $node->expects(self::once())->method('processValue');

        $configSchema->method('getNode')->willReturn($node);

        $configInterface = $this->createMock(ConfigInterface::class);
        $configInterface->expects($this->once())
            ->method('getConfigSchema')
            ->willReturn($configSchema)
        ;

        $config = [
            'node' => 'test',
        ];

        $configProcessor = new ConfigProcessor();
        $configProcessor->processConfig($config, $configInterface);
    }

    public function testProcessConfig(): void
    {
        $configProcessor = new ConfigProcessor();

        $config = $this->getConfig();

        $configInterface = $this->createMock(ConfigInterface::class);
        $configInterface->expects($this->once())
            ->method('getConfigSchema')
            ->willReturn($this->getSchema())
        ;

        $configProcessor->processConfig($config, $configInterface);
    }

    public static function invalidConfigProvider(): \Iterator
    {
        yield 'With invalid key' => [[
            'parameter' => [
                'parameter1' => 'value1',
                'parameter2' => 2,
                'parameter3' => true,
                'parameter4' => 1.2,
                'parameter5' => [
                    [
                        'key1' => 'value',
                        'key2' => 21,
                    ],
                ],
            ],
            'services' => [
                '_default' => [
                    'bind' => [
                        'string $param' => '%parameter1%',
                    ],
                ],
                'App\\' => [
                    'autowire' => true,
                ],
                'App\Service\Test' => [
                    'lazy' => true,
                ],
            ],
        ], NodeNotFoundException::class];

        yield 'With invalid type' => [[
            'parameters' => [
                'parameter1' => 'value1',
                'parameter2' => 2,
                'parameter3' => true,
                'parameter4' => 1.2,
                'parameter5' => [
                    [
                        'key1' => 'value',
                        'key2' => 21,
                    ],
                ],
            ],
            'services' => [
                '_default' => [
                    'bind' => [
                        'string $param' => '%parameter1%',
                    ],
                ],
                'App\\' => [
                    'autowire' => 123,
                ],
                'App\Service\Test' => [
                    'lazy' => true,
                ],
            ],
        ], InvalidNodeTypeException::class];

        yield 'With missing required field' => [[
            'parameters' => [
                'parameter1' => 'value1',
                'parameter2' => 2,
                'parameter3' => true,
                'parameter4' => 1.2,
                'parameter5' => [
                    [
                        'key1' => 'value',
                        'key2' => 21,
                    ],
                ],
            ],
        ], ConfigProcessingException::class];

        yield 'With missing required field #2' => [[
            'parameters' => [
                'parameter1' => 'value1',
                'parameter2' => 2,
                'parameter3' => true,
                'parameter4' => 1.2,
                'parameter5' => [
                    [
                        'key1' => 'value',
                        'key2' => 21,
                    ],
                ],
            ],
            'services' => [
                '_default' => [
                ],
                'App\\' => [
                    'autowire' => true,
                ],
                'App\Service\Test' => [
                    'lazy' => true,
                ],
            ],
        ], ConfigProcessingException::class];

        yield 'With invalid object node schema' => [[
            'parameters' => [
                'parameter1' => 'value1',
                'parameter2' => 2,
                'parameter3' => true,
                'parameter4' => 1.2,
                'parameter5' => [
                    [
                        'key1' => 'value',
                        'key2' => 21,
                    ],
                ],
            ],
            'services' => [
                '_default' => [
                    'bind' => [
                        'string $param' => '%parameter1%',
                    ],
                ],
                'App\\' => [
                    'invalid_node' => true,
                ],
                'App\Service\Test' => [
                    'lazy' => true,
                ],
            ],
        ], NodeNotFoundException::class];
    }

    /**
     * @param array<string, mixed>     $config
     * @param class-string<\Throwable> $exceptionClass
     */
    #[DataProvider('invalidConfigProvider')]
    public function testWithInvalidConfig(array $config, string $exceptionClass): void
    {
        $configProcessor = new ConfigProcessor();

        $this->expectException($exceptionClass);

        $configInterface = $this->createMock(ConfigInterface::class);
        $configInterface->expects($this->once())
            ->method('getConfigSchema')
            ->willReturn($this->getSchema())
        ;

        $configProcessor->processConfig($config, $configInterface);
    }

    /**
     * @return array<string, mixed>
     */
    private function getConfig(): array
    {
        return [
            'parameters' => [
                'parameter1' => 'value1',
                'parameter2' => 2,
                'parameter3' => true,
                'parameter4' => 1.2,
                'parameter5' => [
                    [
                        'key1' => 'value',
                        'key2' => 21,
                    ],
                ],
            ],
            'services' => [
                '_default' => [
                    'bind' => [
                        'string $param' => '%parameter1%',
                    ],
                ],
                'App\\' => [
                    'autowire' => true,
                ],
                'App\Service\Test' => [
                    'lazy' => true,
                ],
            ],
        ];
    }

    private function getSchema(): ConfigSchema
    {
        return new ConfigSchema(
            new ObjectVariableNode('parameters'),
            (new ObjectNode('services', new ConfigSchema(
                new ObjectNode('_default', new ConfigSchema(
                    (new ObjectVariableNode('bind', new StringPrototype()))->required(),
                )),
                new ObjectNode('*', new ConfigSchema(
                    (new BooleanNode('autowire'))->defaultValue(true),
                    (new BooleanNode('lazy'))->defaultValue(false),
                )),
            )))->required()
        );
    }
}
