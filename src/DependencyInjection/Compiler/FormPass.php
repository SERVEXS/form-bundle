<?php

/*
 * This file is part of the GenemuFormBundle package.
 *
 * (c) Olivier Chauvel <olivier@generation-multiple.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Genemu\Bundle\FormBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Add a new twig.form.resources.
 *
 * @author Olivier Chauvel <olivier@generation-multiple.com>
 */
class FormPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        /** @var string[] $resources */
        $resources = $container->getParameter('twig.form.resources');

        foreach (['div', 'jquery', 'stylesheet'] as $template) {
            $resources[] = '@GenemuForm/Form/' . $template . '_layout.html.twig';
        }

        $container->setParameter('twig.form.resources', $resources);
    }
}
