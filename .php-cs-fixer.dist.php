<?php

declare(strict_types=1);

$finder = PhpCsFixer\Finder::create()
    ->in([
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ]);

$config = new PhpCsFixer\Config();

// https://cs.symfony.com/doc/ruleSets/index.html
// https://cs.symfony.com/doc/rules/

return $config->setRules([
//    '@Symfony' => true,
    '@PHP82Migration' => true,
])->setFinder($finder);
