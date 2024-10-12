<?php

declare(strict_types=1);

namespace Kaizen\Components\Config;

use Kaizen\Components\Config\Schema\ConfigSchema;

interface ConfigInterface
{
    public function getConfigSchema(): ConfigSchema;
}