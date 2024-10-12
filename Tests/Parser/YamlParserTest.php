<?php

declare(strict_types=1);

namespace Kaizen\Components\Config\Tests\Parser;

use Kaizen\Components\Config\Exception\InvalidFormatException;
use Kaizen\Components\Config\Exception\ParsingException;
use Kaizen\Components\Config\Parser\YamlParser;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(YamlParser::class)]
class YamlParserTest extends TestCase
{
    public function testParseValidYaml(): void
    {
        $yamlParser = new YamlParser();

        $fileContent = <<<'YAML'
        test_array:
          default:
            test: okok
        test_scalar: scalar
        test_string: string
        test_boolean: true
        test_int: 123
        YAML;

        $actual = $yamlParser->parse($fileContent);

        $expectedResult = [
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

        $this->assertSame($expectedResult, $actual);
    }

    public function testParseValidYamlWithConstValues(): void
    {
        $yamlParser = new YamlParser();

        $fileContent = <<<'YAML'
        test_enum: !php/const Kaizen\Components\Config\Tests\Fixtures\Constant\TestEnum::ENUM_CONST
        test_const_interface: !php/const Kaizen\Components\Config\Tests\Fixtures\Constant\TestConstInterface::INTERFACE_CONST
        test_const_class: !php/const Kaizen\Components\Config\Tests\Fixtures\Constant\TestConstClass::CLASS_CONST_STRING
        test_const_array: !php/const Kaizen\Components\Config\Tests\Fixtures\Constant\TestConstClass::CLASS_CONST_ARRAY
        YAML;

        $actual = $yamlParser->parse($fileContent);

        $expectedResult = [
            'test_enum' => 'string ENUM_CONST',
            'test_const_interface' => 'string INTERFACE_CONST',
            'test_const_class' => 'string CLASS_CONST',
            'test_const_array' => [1, '2', true, 1.20],
        ];

        $this->assertEquals($expectedResult, $actual);
    }

    public function testParseValidYamlWithInvalidClass(): void
    {
        $yamlParser = new YamlParser();

        $fileContent = <<<'YAML'
        exists: !php/const Kaizen\Components\Config\Tests\Fixtures\Constant\TestEnum::ENUM_CONST
        not_exists: !php/const Kaizen\Components\Config\Tests\Fixtures\Constant\NotExistingClassTwo::ENUM_CONST
        YAML;

        $this->expectException(ParsingException::class);

        $yamlParser->parse($fileContent);
    }

    public static function fileProvider(): \Iterator
    {
        yield 'yaml' => ['valid.yaml', true];

        yield 'xml' => ['valid.xml', false];
    }

    #[DataProvider('fileProvider')]
    public function testSupportsFile(string $file, bool $expectedResult): void
    {
        /** @var string $path */
        $path = realpath(sprintf(
            '%1$s%2$s..%2$sFixtures%2$sconfig%2$syaml%2$s%3$s',
            __DIR__,
            DIRECTORY_SEPARATOR,
            $file
        ));

        $yamlParser = new YamlParser();

        $this->assertEquals($expectedResult, $yamlParser->supports($path));
    }

    public function testParseValidYamlWithInvalidConst(): void
    {
        $yamlParser = new YamlParser();

        $fileContent = <<<'YAML'
        test_enum: !php/const Kaizen\Components\Config\Tests\Fixtures\Constant\TestEnum::NOT_EXISTS
        test_const_interface: !php/const Kaizen\Components\Config\Tests\Fixtures\Constant\TestConstInterface::NOT_EXISTS
        YAML;

        $this->expectException(ParsingException::class);

        $yamlParser->parse($fileContent);
    }

    public static function invalidYamlProvider(): \Iterator
    {
        yield 'With invalid indentation' => [<<<'YAML'
        key1:
          subkey1: value1
            subkey2: value2
        YAML];

        yield 'With invalid anchors alias' => [<<<'YAML'
        person: &person
          name: John Doe
          age: 30
        another_person: *unknown_anchor 
        YAML];

        // TODO Reactivate those rules when using a more accurate yaml parser
        //
        // yield 'With missing colon' => [<<<YAML
        // key1 "value1"
        // YAML];
        //
        // yield 'With unquoted special characters' => [<<<YAML
        // email: john.doe@domain.com:8080
        // YAML];
        //
        // yield 'With duplicated keys' => [<<<YAML
        // key1: value1
        // key1: value2
        // YAML];
        //
        // yield 'With non-aligned sequences' => [<<<YAML
        // fruits:
        //     - apple
        //      - pineapple
        //     - orange
        // YAML];
        //
        // yield 'With Invalid multiline string' => [<<<YAML
        // description: |
        //     This is a multiline string
        //      with inconsistent indentation
        //     and this line is invalid
        // YAML];
    }

    #[DataProvider('invalidYamlProvider')]
    public function testParseInvalidYaml(string $fileContent): void
    {
        $yamlParser = new YamlParser();

        self::expectException(InvalidFormatException::class);

        $yamlParser->parse($fileContent);
    }
}
