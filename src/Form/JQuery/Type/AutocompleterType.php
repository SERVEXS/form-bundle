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
 * @author Olivier Chauvel <olivier@generation-multiple.com>
 */
class AutocompleterType extends AbstractType
{
    private $widget;

    public function __construct($widget)
    {
        $this->widget = $widget;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addViewTransformer(new ChoiceToJsonTransformer(
            $options['choice_list'],
            $options['ajax'],
            $this->widget,
            $options['multiple']
        ));
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $datas = json_decode($form->getViewData(), true);
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

        $view->vars = array_replace($view->vars, [
            'autocompleter_value' => $value,
            'route_name' => $options['route_name'],
            'free_values' => $options['free_values'],
        ]);

        // Adds a custom block prefix
        array_splice(
            $view->vars['block_prefixes'],
            array_search($this->getBlockPrefix() . 'AutocompleterType.php' . $view->vars['name'], $view->vars['block_prefixes']),
            0,
            'genemu_jqueryautocompleter'
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $widget = $this->widget;

        $resolver->setDefaults([
            'route_name' => null,
            'ajax' => fn (Options $options, $previousValue) => !empty($options['route_name']) || $options['free_values'],
            'choice_list' => function (Options $options, $previousValue) use ($widget) {
                if (!in_array($widget, ['entity', 'document', 'model'])) {
                    return new AjaxSimpleChoiceList($options['choices'], $options['ajax']);
                }

                return $previousValue;
            },
            'freeValues' => false,
            'free_values' => function (Options $options, $previousValue) {
                if ($options['multiple']) {
                    return false;
                }

                return $options['freeValues'] ?: $previousValue;
            },
        ]);
    }

    public function getParent(): ?string
    {
        if (in_array($this->widget, ['entity', 'document', 'model'], true)) {
            return 'genemu_ajax' . $this->widget;
        }

        return $this->widget;
    }

    public function getBlockPrefix(): string
    {
        return 'genemu_jqueryautocompleter_' . $this->widget;
    }
}
