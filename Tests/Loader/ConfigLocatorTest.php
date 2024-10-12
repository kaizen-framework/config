<?php

namespace Kaizen\Components\Config\Tests\Loader;

use Kaizen\Components\Config\Exception\InvalidFormatException;
use Kaizen\Components\Config\Exception\ParsingException;
use Kaizen\Components\Config\Exception\ResourceNotFoundException;
use Kaizen\Components\Config\Loader\ConfigLocator;
use Kaizen\Components\Config\Parser\YamlParser;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(ConfigLocator::class)]
class ConfigLocatorTest extends TestCase
{
    public function testLocateYamlResource(): void
    {
        $basePath = sprintf('%1$s%2$s..%2$sFixtures%2$sconfig%2$syaml', __DIR__, DIRECTORY_SEPARATOR);
        $configLocator = new ConfigLocator($basePath, [new YamlParser()]);

        $config = $configLocator->locate('valid.yaml');

        $expectedConfig = [
            'test_array' => [
                'default' => [
                    'test' => 'okok',
                ],
            ],
            'test_scalar' => 'scalar',
            'test_string' => 'string',
            'test_boolean' => true,
            'test_int' => 123,
        ];

        $this->assertSame($expectedConfig, $config);
    }

    public function testLocateNotSupportedResource(): void
    {
        $basePath = sprintf('%1$s%2$s..%2$sFixtures%2$sconfig%2$syaml', __DIR__, DIRECTORY_SEPARATOR);
        $configLocator = new ConfigLocator($basePath, [new YamlParser()]);

        $this->expectException(ParsingException::class);
        $this->expectExceptionMessage('None of the parsers provided are able to support the "/srv/app/src/Components/Config/Tests/Fixtures/config/yaml/valid.xml" file');
        $configLocator->locate('valid.xml');
    }

    public function testLocateNotExistingResource(): void
    {
        $basePath = sprintf('%1$s%2$s..%2$sFixtures%2$sconfig%2$syaml', __DIR__, DIRECTORY_SEPARATOR);
        $configLocator = new ConfigLocator($basePath, [new YamlParser()]);

        $this->expectException(ResourceNotFoundException::class);

        $configLocator->locate('not_exists.yaml');
    }

    public function testLocateInvalidResource(): void
    {
        $basePath = sprintf('%1$s%2$s..%2$sFixtures%2$sconfig%2$syaml', __DIR__, DIRECTORY_SEPARATOR);
        $configLocator = new ConfigLocator($basePath, [new YamlParser()]);

        $this->expectException(InvalidFormatException::class);

        $configLocator->locate('invalid.yaml');
    }
}
