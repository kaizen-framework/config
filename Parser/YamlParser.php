<?php

declare(strict_types=1);

namespace Kaizen\Components\Config\Parser;

use Kaizen\Components\Config\Exception\InvalidFormatException;
use Kaizen\Components\Config\Exception\ParsingException;

class YamlParser implements ParserInterface
{
    #[\Override]
    public function parse(string $fileContent): array
    {
        /** @var array<string, mixed>|false $yaml */
        $yaml = @yaml_parse($fileContent, 0, $nbDocs, callbacks: [
            '!php/const' => $this->parseTags(...),
        ]);

        if (false === $yaml) {
            $error = error_get_last();

            if (null === $error) {
                throw new \RuntimeException('Unexpected error happen while parsing configuration file.');
            }

            throw new InvalidFormatException($error['message']);
        }

        return $yaml;
    }

    #[\Override]
    public function supports(string $path): bool
    {
        return file_exists($path) && in_array(pathinfo($path, PATHINFO_EXTENSION), ['yml', 'yaml']);
    }

    /**
     * @return null|array<int|string, mixed>|bool|float|int|string
     *
     * @throws ParsingException
     */
    private function parseTags(string $value, string $tag): null|array|bool|float|int|string
    {
        [0 => $class, 1 => $constant] = explode('::', $value);

        if (enum_exists($class)) {
            if (!defined($value)) {
                throw new ParsingException(sprintf(
                    'Case "%s" does not exist in enum "%s".',
                    $class,
                    $constant
                ));
            }

            /** @phpstan-ignore-next-line All checks are already performed so it will not throw error */
            $constValue = constant($value)->value;
        } elseif (class_exists($class) || interface_exists($class)) {
            if (!defined($value)) {
                throw new ParsingException(sprintf(
                    'Constant "%s" does not exist in class "%s".',
                    $class,
                    $constant
                ));
            }

            $constValue = constant($value);
        } else {
            throw new ParsingException(sprintf(
                'Class, enum or interface "%s" does not exist, for the tag "%s" with value "%s"',
                $class,
                $tag,
                $value
            ));
        }

        if (!in_array(gettype($constValue), ['string', 'int', 'float', 'bool', 'null', 'array', 'NULL'], true)) {
            throw new ParsingException(sprintf(
                'Constant "%s" does not have a proper type',
                $value
            ));
        }

        /** @phpstan-var null|array<int|string, mixed>|scalar */
        return $constValue;
    }
}
