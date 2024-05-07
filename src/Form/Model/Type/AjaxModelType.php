<?php

/*
 * This file is part of the GenemuFormBundle package.
 *
 * (c) Olivier Chauvel <olivier@generation-multiple.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Genemu\Bundle\FormBundle\Form\Model\Type;

use Form\Model\ChoiceList\AjaxModelChoiceList;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * AjaxModelType
 *
 * @author Olivier Chauvel <olivier@generation-multiple.com>
 */
class AjaxModelType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'template'          => 'choice',
            'multiple'          => false,
            'expanded'          => false,
            'class'             => null,
            'property'          => null,
            'query'             => null,
            'choices'           => [],
            'preferred_choices' => [],
            'ajax'              => false,
            'choice_list'       => function (Options $options, $previousValue) {
                if (null === $previousValue) {
                    if (!isset($options['choice_list'])) {
                        return new AjaxModelChoiceList(
                            $options['class'],
                            $options['property'],
                            $options['choices'],
                            $options['query'],
                            $options['ajax']
                        );
                    }
                }

                return null;
            },
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent(): ?string
    {
        return 'model';
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'genemu_ajaxmodel';
    }
}
