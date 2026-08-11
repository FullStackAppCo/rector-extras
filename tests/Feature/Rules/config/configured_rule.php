<?php

declare(strict_types=1);

use Fsac\RectorExtras\Rules\HelperFunctionToFacadeRector;
use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withRules([HelperFunctionToFacadeRector::class]);
