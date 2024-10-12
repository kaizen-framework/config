<?php

declare(strict_types=1);

namespace Kaizen\Components\Config\Loader;

use Kaizen\Components\Config\Exception\InvalidFormatException;
use Kaizen\Components\Config\Exception\ParsingException;
use Kaizen\Components\Config\Exception\ResourceNotFoundException;
use Kaizen\Components\Config\Parser\ParserInterface;

readonly class ConfigLocator
{
    /**
     * @param ParserInterface[] $parsers
     */
    public function __construct(
        private string $rootDir,
        private array $parsers
    ) {}

    /**
     * @return array<string, mixed>
     *
     * @throws ResourceNotFoundException
     * @throws InvalidFormatException
     * @throws ParsingException
     */
    public function locate(string $filename): array
    {
        $realPath = realpath($this->rootDir.DIRECTORY_SEPARATOR.$filename);

        if (false === $realPath) {
            throw new ResourceNotFoundException(sprintf('Unable to locate configuration file "%s"', $filename));
        }

        $parser = $this->findParser($realPath);

        /** @var string $fileContents */
        $fileContents = file_get_contents($realPath);

        return $parser->parse($fileContents);
    }

    /**
     * @throws ParsingException
     */
    private function findParser(string $filePath): ParserInterface
    {
        $parsers = [];

        foreach ($this->parsers as $parser) {
            if ($parser->supports($filePath)) {
                $parsers[] = $parser;
            }
        }

        if ([] === $parsers) {
            throw new ParsingException(sprintf(
                'None of the parsers provided are able to support the "%s" file',
                $filePath
            ));
        }

        if (count($parsers) > 1) {
            throw new ParsingException(sprintf(
                'More than one parser can support "%s" file, conflict parsers : "%s"',
                $filePath,
                implode(', ', array_map(static fn (ParserInterface $parser): string => $parser::class, $parsers))
            ));
        }

        return current($parsers);
    }
}
