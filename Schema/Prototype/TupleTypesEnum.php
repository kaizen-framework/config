<?php

namespace Kaizen\Components\Config\Schema\Prototype;

enum TupleTypesEnum: string
{
    case STRING = 'string';
    case INTEGER = 'integer';
    case FLOAT = 'double';
    case BOOLEAN = 'boolean';
    case SCALAR = 'scalar';
}
