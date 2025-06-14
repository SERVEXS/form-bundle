<?php

/*
 * This file is part of the GenemuFormBundle package.
 *
 * (c) Olivier Chauvel <olivier@generation-multiple.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Genemu\Bundle\FormBundle\Tests\Form\Type;

use Genemu\Bundle\FormBundle\Tests\Form\Extension\TypeExtensionTest;
use Locale;
use Symfony\Component\Form\Test\TypeTestCase as BaseTypeTestCase;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * @author Olivier Chauvel <olivier@generation-multiple.com>
 */
abstract class TypeTestCase extends BaseTypeTestCase
{
    protected $requestStack;

    public function setUp(): void
    {
        parent::setUp();

        Locale::setDefault('en');
    }

    protected function getExtensions()
    {
        return [
            new TypeExtensionTest($this->createRequestStackMock()),
        ];
    }

    protected function createRequestStackMock()
    {
        return $this->requestStack = $this->getMock(RequestStack::class);
    }
}
