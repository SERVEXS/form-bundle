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
use Symfony\Component\Form\Extension\Core\Type\LanguageType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Select2LanguageType to JQueryLib.
 *
 * @author Bilal Amarni <bilal.amarni@gmail.com>
 * @author Chris Tickner <chris.tickner@gmail.com>
 * @author Benjamin Schumacher <benschumi@hotmail.fr>
 */
class Select2LanguageType extends AbstractType
{
    private $configs;

    public function __construct(array $configs = [])
    {
        $this->configs = $configs;
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['configs'] = $options['configs'];

        // Adds a custom block prefix
        array_splice(
            $view->vars['block_prefixes'],
            array_search($this->getBlockPrefix() . 'Select2LanguageType.php' . $view->vars['name'], $view->vars['block_prefixes']),
            0,
            'genemu_jqueryselect2'
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $defaults = $this->configs;
        $resolver
            ->setDefaults([
                'configs' => $defaults,
                'transformer' => null,
            ])
            ->setNormalizer(
                'configs',
                fn (Options $options, $configs) => array_merge($defaults, $configs)
            )
        ;
    }

    public function getParent(): ?string
    {
        return LanguageType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'genemu_jqueryselect2';
    }
}
