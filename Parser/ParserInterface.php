<?php

namespace Kaizen\Components\Config\Parser;

use Kaizen\Components\Config\Exception\InvalidFormatException;
use Kaizen\Components\Config\Exception\ParsingException;

interface ParserInterface
{
    /**
     * @return array<string, mixed>
     *
     * @throws InvalidFormatException
     * @throws ParsingException
     */
    public function parse(string $fileContent): array;

    public function supports(string $path): bool;
}
