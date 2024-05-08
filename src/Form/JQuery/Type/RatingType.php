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
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * RatingType.
 *
 * @author Olivier Chauvel <olivier@generation-multiple.com>
 * @author Tom Adam <tomadam@instantiate.co.uk>
 */
class RatingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->setAttribute('configs', $options['configs']);
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['configs'] = $form->getConfig()->getAttribute('configs');
        if (!isset($view->vars['configs']['required'])) {
            $view->vars['configs']['required'] = $options['required'];
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'number' => 5,
            'configs' => [],
            'expanded' => true,
            'choices' => function (Options $options) {
                $choices = [];
                for ($i = 1; $i <= $options['number']; ++$i) {
                    $choices[$i] = null;
                }

                return $choices;
            },
        ]);

        $resolver->setNormalizer('expanded', fn (Options $options, $value) => true);
    }

    public function getParent(): ?string
    {
        return \Symfony\Component\Form\Extension\Core\Type\ChoiceType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'genemu_jqueryrating';
    }
}
