# Config Component

The Config component allows you to build a schema that represents your application's configuration with validation for
types and rules (required fields, default values, etc.).

## Usage

To create your schema you need to create a class that implement the `ConfigInterface`.

You can use the `ConfigSchemaBuilder` to construct your schema with methods that represent different nodes and their validation rules. For example:`

```php
class Configuration implements \Kaizen\Components\Config\ConfigInterface
{
    public function getConfigSchema(): \Kaizen\Components\Config\Schema\ConfigSchema
    {
        $schemaBuilder = new \Kaizen\Components\Config\Schema\ConfigSchemaBuilder();

        $schema = $schemaBuilder
            ->boolean('booleanNode')
                ->required()
                ->defaultValue(false)
                ->buildNode()
            ->integer('integerNode')
                ->min(1)
                ->max(10)
                ->defaultValue(3)
                ->buildNode()
            ->array('arrayNode')
                ->withPrototype(new \Kaizen\Components\Config\Schema\Prototype\IntegerPrototype())
                ->buildNode()
            ->array('arrayObjectNode')
                ->withPrototype(new \Kaizen\Components\Config\Schema\Prototype\ObjectPrototype(
                    new \Kaizen\Components\Config\Schema\Node\StringNode('arrayObjectStringNode'),
                    new \Kaizen\Components\Config\Schema\Node\BooleanNode('arrayObjectBooleanNode'),
                ))
                ->buildNode()
            ->child('objectNode')
                ->float('childFloatNode')
                    ->max(10.5)
                    ->required()
                    ->buildNode()
                ->string('childStringNode')
                    ->required()
                    ->buildNode()
                ->buildChildNode()
            ->build();
    }
}
```

Let's take a YAML config that satisfies this schema:

```yaml
booleanNode: true
integerNode: 1
arrayNode: # Or arrayNode: [1,2,3]
  - 1
  - 2
  - 3
arrayObjectNode:
  - {arrayObjectStringNode: a string, arrayObjectBooleanNode: false}
  - {arrayObjectStringNode: another string, arrayObjectBooleanNode: true}
# ...
objectNode:
  childFloatNode: 5.5
  childStringNode: a string
```

To locate your config files, use the `ConfigLocator`:

```php
// The second argument is an array of parsers that transform the loaded file into a usable PHP array
$configLocator = new ConfigLocator('root/path/of/the/config/files', [new \Kaizen\Components\Config\Parser\YamlParser()]);
$config = $configLocator->locate('some_file.yaml');
```

> **Note:** Make sure to load only files that are supported by the parsers you've added to the Config locator constructor.

Currently, only a YAML parser is available. However, you can create your own parser by implementing the `ParserInterface`:

```php
class CustomParser implements \Kaizen\Components\Config\Parser\ParserInterface
{
    public function parse(string $fileContent): array
    {
        // Logic to parse your document
    }

    public function supports(string $path): bool
    {
        // Check whether the parser should parse your file or not
        return pathinfo($path, PATHINFO_EXTENSION) === 'desired.extension';
    }
}
```

Once your schema is built, use the `ConfigProcessor` to validate it:

```php
$configProcessor = new \Kaizen\Components\Config\Processor\ConfigProcessor();
$configProcessor->processConfig($config, new Configuration());
```

The processor will set default values and check the rules. Your configuration is now extracted and validated!`

## Nodes

  - scalarNode (int, float, boolean, string, null)
  - numericNode (int, float)
  - intNode
  - floatNode
  - stringNode
  - booleanNode
  - numberNode (int and float)
  - enumNode
  - arrayNode (can include prototype to define the shape)

### Prototypes

Prototypes define the expected values in an iterable node. For example:

```php
$node = new ArrayNode('node key', new ScalarPrototype());
```

This ensures the array contains only scalar values.

While there are built-in prototypes for common use cases, you can create custom validations by implementing the `PrototypeInterface`:

```php
class CustomPrototype implements \Kaizen\Components\Config\Schema\Prototype\PrototypeInterface
{
    public function validateArray(array $array): void
    {
        // Perform your custom validation
        // Throw an InvalidNodeTypeException if conditions are not met
    }
}

$customArrayNodePrototype = new \Kaizen\Components\Config\Schema\Node\ArrayNode('node key', new CustomPrototype());
```

#### Built-in Prototypes:

- **TuplePrototype**: The array must respect specific types and order
```php
$customArrayNodePrototype = new \Kaizen\Components\Config\Schema\Node\ArrayNode(
  'node key',
  new \Kaizen\Components\Config\Schema\Prototype\TuplePrototype(
    \Kaizen\Components\Config\Schema\Prototype\TupleTypesEnum::BOOLEAN,
    \Kaizen\Components\Config\Schema\Prototype\TupleTypesEnum::INTEGER,
  )
);

// Valid array
[true, 123]

// Invalid arrays
[123, true];
[true, '123']
[true, 12.3]
[true, true]
```

- **StringPrototype**: The array should contain only strings

- **IntegerPrototype**: The array should contain only integers

- **ObjectPrototype**: The array should contain objects
```php
$node = new \Kaizen\Components\Config\Schema\Node\ArrayNode(
    'node_key',
    new \Kaizen\Components\Config\Schema\Prototype\ObjectPrototype(
        new \Kaizen\Components\Config\Schema\Node\StringNode('node_sub_key_1'),
        new \Kaizen\Components\Config\Schema\Node\IntegerNode('node_sub_key_2'),
    )
);

// Valid array
[
    ['node_sub_key_1' => 'value', 'node_sub_key_2' => 123]
];

// Invalid arrays
[
    ['node_sub_key_11' => 'value', 'node_sub_key_2' => 123]
];
[
    ['node_sub_key_1' => true, 'node_sub_key_2' => 123]
];
```

```yaml
# Valid YAML
node_key:
  - {node_sub_key_1: value, node_sub_key_2: 123}

# Invalid YAML
node_key:
  - {node_sub_key_11: value, node_sub_key_2: 123}

# Invalid YAML
node_key:
  - {node_sub_key_1: true, node_sub_key_2: 123}

```

- **ScalarPrototype**: The array should contain only scalar values (string, integer, boolean, float, null)`