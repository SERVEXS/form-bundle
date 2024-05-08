<?php

use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/src',
    ])
    ->withSets([
        \Rector\Symfony\Set\SymfonySetList::SYMFONY_30
    ])
    ->withRules([
       //
    ]);
