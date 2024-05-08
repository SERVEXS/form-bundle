<?php

/*
 * This file is part of the GenemuFormBundle package.
 *
 * (c) Olivier Chauvel <olivier@generation-multiple.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Genemu\Bundle\FormBundle\Form\Core\Type;

use Locale;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * TinymceType.
 *
 * @author Olivier Chauvel <olivier@generation-multiple.com>
 */
class TinymceType extends AbstractType
{
    public function __construct(private readonly array $options)
    {
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['configs'] = $options['configs'];
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $configs = array_merge(
            [
                'language' => Locale::getDefault(),
            ],
            $this->options
        );

        $resolver
            ->setDefaults([
                'configs' => [],
                'required' => false,
                'theme' => 'default',
            ])

            ->setAllowedTypes([
                'configs' => 'array',
                'theme' => 'string',
            ]);
        $resolver->setNormalizer('configs', fn (Options $options, $value) => array_merge($configs, $value));
    }

    public function getParent(): ?string
    {
        return TextareaType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'genemu_tinymce';
    }
}
