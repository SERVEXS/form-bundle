<?php

use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/src',
    ])
    ->withRules([
        \Rector\Symfony\Set\SymfonySetList::SYMFONY_28
    ]);
