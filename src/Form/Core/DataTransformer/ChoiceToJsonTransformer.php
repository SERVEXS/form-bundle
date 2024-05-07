<?php

/*
 * This file is part of the GenemuFormBundle package.
 *
 * (c) Olivier Chauvel <olivier@generation-multiple.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Genemu\Bundle\FormBundle\Form\Core\DataTransformer;

use Symfony\Component\Form\ChoiceList\ChoiceListInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\Exception\UnexpectedTypeException;

/**
 * ChoiceToJsonTransformer
 *
 * @author Olivier Chauvel <olivier@generation-multiple.com>
 */
class ChoiceToJsonTransformer implements DataTransformerInterface
{
    public function __construct(
        protected ChoiceListInterface $choiceList,
        protected bool $ajax = false,
        protected string $widget = 'choice',
        protected bool $multiple = false
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function transform($choices)
    {
        if (empty($choices)) {
            return null;
        }

        if (is_scalar($choices)) {
            $choices = [$choices];
        }

        if (!is_array($choices)) {
            throw new UnexpectedTypeException($choices, 'array');
        }

        $choices = $this->choiceList->getChoicesForValues($choices);

        if (!$this->multiple) {
            $choices = current($choices);
        }

        return json_encode($choices);
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($json)
    {
        $choices = json_decode(is_array($json) ? current($json) : $json, true);

        // Single choice list
        if (!$this->multiple) {

            if (empty($choices)) {
                return '';
            }

            if (!$this->isSimpleValue($choices)) {
                throw new TransformationFailedException('The format of the json array is bad');
            }

            $this->addAjaxChoices($choices);

            return $choices['value'];
        }

        if (empty($choices)) {
            return [];
        }

        if (!$this->isArrayValue($choices)) {
            throw new TransformationFailedException('The format of the json array is bad');
        }

        $choices = array_unique($choices, SORT_REGULAR);

        $values = [];

        foreach ($choices as $choice) {
            $this->addAjaxChoices($choice);

            $values[] = $choice['value'];
        }

        return $values;
    }

    private function addAjaxChoices(&$choices)
    {
        if ($this->ajax && !in_array($this->widget, ['entity', 'document', 'model'])) {
            $this->choiceList->addAjaxChoice($choices);
        }
    }

    /**
     * Checks if the argument has 'value' and 'label' keys
     */
    private function isSimpleValue($array)
    {
        return is_array($array)
            && array_key_exists('value', $array)
            && array_key_exists('label', $array);
    }

    /**
     * Checks if the argument is an array of simple values
     */
    private function isArrayValue($array)
    {
        foreach ($array as $item) {
            if (!$this->isSimpleValue($item)) {
                return false;
            }
        }
        return true;
    }
}
