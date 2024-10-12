<?php

declare(strict_types=1);

namespace Kaizen\Components\Config\Tests;

use Kaizen\Components\Config\ConfigInterface;
use Kaizen\Components\Config\Loader\ConfigLocator;
use Kaizen\Components\Config\Parser\YamlParser;
use Kaizen\Components\Config\Processor\ConfigProcessor;
use Kaizen\Components\Config\Schema\ConfigSchema;
use Kaizen\Components\Config\Schema\ConfigSchemaBuilder;
use Kaizen\Components\Config\Schema\Node\StringNode;
use Kaizen\Components\Config\Schema\Prototype\ObjectPrototype;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversNothing]
class ConfigFullWorkflowTest extends TestCase
{
    public function testConfigFullWorkflow(): void
    {
        $schema = $this->getConfigInterface();

        $rootPath = sprintf(
            '%s/Fixtures/config/yaml',
            __DIR__
        );

        $configLocator = new ConfigLocator($rootPath, [new YamlParser()]);

        $config = $configLocator->locate('config.yml');

        $configProcessor = new ConfigProcessor();

        $configProcessor->processConfig($config, $schema);

        $expectedConfig = [
            'kaizen' => [
                'config' => [
                    'databases' => [
                        ['database_url' => 'mysql://user:password@localhost:3360/database', 'database_version' => '12.3'],
                        ['database_url' => 'mysql://user:password@localhost:3361/database2', 'database_version' => '1.0.0'],
                    ],
                    'parameters' => [
                        'param1' => 'value1',
                        'param2' => 12,
                        'param3' => 1.2,
                        'param4' => ['1', 2, true],
                        'param5' => ['subkey' => 1],
                        'param6' => ['array', 'param'],
                        'param7' => true,
                        'param8' => null,
                        'param9' => 'string ENUM_CONST',
                    ],
                ],
            ],
        ];

        $this->assertEquals($expectedConfig, $config);
    }

    private function getConfigInterface(): ConfigInterface
    {
        return new class implements ConfigInterface {
            public function getConfigSchema(): ConfigSchema
            {
                $configSchemaBuilder = new ConfigSchemaBuilder();

                return $configSchemaBuilder
                    ->child('kaizen')
                    ->child('config')
                    ->array('databases')
                    ->withPrototype(new ObjectPrototype(
                        (new StringNode('database_url'))->required(),
                        (new StringNode('database_version'))->defaultValue('1.0.0')
                    ))
                    ->buildNode()
                    ->objectVariable('parameters')
                    ->buildNode()
                    ->buildChildNode()
                    ->buildChildNode()
                    ->string('okok')
                    ->buildNode()
                    ->build()
                ;
            }
        };
    }
}
