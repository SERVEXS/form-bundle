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

use Genemu\Bundle\FormBundle\Form\Core\DataTransformer\ChoiceToJsonTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Adam Kuśmierz <adam@kusmierz.be>
 */
class TokeninputType extends AbstractType
{
    public function __construct(private $widget)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if (isset($options['configs']['tokenLimit']) && is_numeric(
            $options['configs']['tokenLimit']
        ) && $options['configs']['tokenLimit'] > 0) {
            $options['multiple'] = (1 != $options['configs']['tokenLimit']);
        }

        if (!$options['multiple']) {
            $options['configs']['tokenLimit'] = 1;
        }

        $builder
            ->addViewTransformer(
                new ChoiceToJsonTransformer(
                    $options['choice_list'],
                    $options['ajax'],
                    $this->widget,
                    $options['multiple']
                )
            )
            ->setAttribute('choice_list', $options['choice_list'])
            ->setAttribute('route_name', $options['route_name']);
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $datas = json_decode((string) $form->getViewData(), true);
        $value = '';

        if (!empty($datas)) {
            if ($options['multiple']) {
                foreach ($datas as $data) {
                    $value .= $data['label'] . ', ';
                }
            } else {
                $value = $datas['label'];
            }
        }

        $view->vars['tokeninput_value'] = $value;
        $view->vars['configs'] = $options['configs'];
        $view->vars['route_name'] = $form->getConfig()->getAttribute('route_name');

        array_splice(
            $view->vars['block_prefixes'],
            array_search($this->getBlockPrefix() . 'TokeninputType.php' . $view->vars['name'], $view->vars['block_prefixes']),
            0,
            'genemu_jquerytokeninput'
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $widget = $this->widget;

        $defaults = [
            'queryParam' => 'term',
            'preventDuplicates' => true,
            'tokenValue' => 'value',
            'propertyToSearch' => 'label',
            'theme' => 'facebook',
        ];

        $resolver
            ->setDefaults([
                'route_name' => null,
                'ajax' => function (Options $options, $previousValue) {
                    if (null === $previousValue) {
                        if (false === empty($options['route_name'])) {
                            return true;
                        }
                    }

                    return false;
                },
                'choice_list' => function (Options $options, $previousValue) use ($widget) {
                    if (!in_array($widget, ['entity', 'document', 'model'])) {
                        return new AjaxSimpleChoiceList($options['choices'], $options['ajax']);
                    }

                    return $previousValue;
                },
                'configs' => $defaults,
            ]);
        $resolver->setNormalizer('configs', fn (Options $options, $configs) => array_merge($defaults, $configs));
    }

    public function getParent(): ?string
    {
        if (true === in_array($this->widget, ['entity', 'document', 'model'], true)) {
            return 'genemu_ajax' . $this->widget;
        }

        return $this->widget;
    }

    public function getBlockPrefix(): string
    {
        return 'genemu_jquerytokeninput_' . $this->widget;
    }
}
