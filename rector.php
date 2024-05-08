<?php

use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/src',
    ])
    ->withSets([
        \Rector\Set\ValueObject\SetList::PHP_80,
        \Rector\Set\ValueObject\SetList::PHP_81,
        \Rector\Set\ValueObject\SetList::PHP_82,
//        //uncomment for sf6 fixes
//        \Rector\Symfony\Set\SymfonySetList::SYMFONY_60,
//        \Rector\Symfony\Set\SymfonySetList::SYMFONY_61,
//        \Rector\Symfony\Set\SymfonySetList::SYMFONY_62,
//        \Rector\Symfony\Set\SymfonySetList::SYMFONY_63,
//        \Rector\Symfony\Set\SymfonySetList::SYMFONY_64,
    ])
    ->withRules([
       //
    ]);
