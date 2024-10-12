<?php

namespace Kaizen\Components\Config\Schema\Node;

use Kaizen\Components\Config\Schema\ConfigSchema;

interface ParentNodeInterface
{
    public function getChildren(): ConfigSchema;
}
