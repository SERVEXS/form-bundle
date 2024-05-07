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
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * ChosenType to JQueryLib.
 *
 * @author Olivier Chauvel <olivier@generation-multiple.com>
 * @author Bilal Amarni <bilal.amarni@gmail.com>
 */
class ChosenType extends AbstractType
{
    private $widget;

    public function __construct($widget)
    {
        $this->widget = $widget;
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['no_results_text'] = $options['no_results_text'];
        $view->vars['allow_single_deselect'] = $options['allow_single_deselect'];
        $view->vars['disable_search_threshold'] = $options['disable_search_threshold'];

        // Adds a custom block prefix
        array_splice(
            $view->vars['block_prefixes'],
            array_search($this->getBlockPrefix() . 'ChosenType.php' . $view->vars['name'], $view->vars['block_prefixes']),
            0,
            'genemu_jquerychosen'
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'no_results_text' => '',
                'allow_single_deselect' => true,
                'disable_search_threshold' => 0,
            ])
            ->setNormalizer('expanded', fn (Options $options) => false);
    }

    public function getParent(): ?string
    {
        return $this->widget;
    }

    public function getBlockPrefix(): string
    {
        return 'genemu_jquerychosen_' . $this->widget;
    }
}
