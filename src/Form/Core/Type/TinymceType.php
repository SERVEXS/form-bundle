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

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

/**
 * TinymceType
 *
 * @author Olivier Chauvel <olivier@generation-multiple.com>
 */
class TinymceType extends AbstractType
{
    public function __construct(private readonly array $options)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['configs'] = $options['configs'];
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolver $resolver): void
    {
        $configs = array_merge(array(
            'language' => \Locale::getDefault(),
        ),
		$this->options);

        $resolver
            ->setDefaults(array(
                'configs' => array(),
                'required' => false,
                'theme' => 'default',
            ))
            ->setAllowedTypes(array(
                'configs' => 'array',
                'theme' => 'string',
            ))
            ->setNormalizers(array(
                'configs' => function (Options $options, $value) use ($configs) {
                    return array_merge($configs, $value);
                },
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getParent(): ?string
    {
        return TextareaType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'genemu_tinymce';
    }
}
