<?php

/*
 * This file is part of the GenemuFormBundle package.
 *
 * (c) Olivier Chauvel <olivier@generation-multiple.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Genemu\Bundle\FormBundle\Form\JQuery\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * ColorType.
 *
 * @author Olivier Chauvel <olivier@generation-multiple.com>
 */
class ColorType extends AbstractType
{
    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['widget'] = $options['widget'];
        $view->vars['configs'] = $options['configs'];
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'widget' => 'text',
                'configs' => [],
            ])
            ->setAllowedValues([
                'widget' => [
                    'text',
                    'image',
                ],
            ])
        ;
    }

    public function getParent(): ?string
    {
        return 'text';
    }

    public function getBlockPrefix(): string
    {
        return 'genemu_jquerycolor';
    }
}
