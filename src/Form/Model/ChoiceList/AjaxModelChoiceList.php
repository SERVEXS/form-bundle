<?php

/*
 * This file is part of the GenemuFormBundle package.
 *
 * (c) Olivier Chauvel <olivier@generation-multiple.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Genemu\Bundle\FormBundle\Form\Model\ChoiceList;

use Closure;
use Symfony\Bridge\Propel1\Form\ChoiceList\ModelChoiceList;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyPath;

/**
 * AjaxModelChoiceList.
 *
 * @author Olivier Chauvel <olivier@generation-multiple.com>
 */
class AjaxModelChoiceList extends ModelChoiceList
{
    private $propertyPath;

    /**
     * Constructs.
     *
     * @param string        $class
     * @param string        $property
     * @param array|Closure $choices
     * @param QueryObject   $qo
     * @param bool          $ajax
     */
    public function __construct($class, $property = null, $choices = [], $qo = null, private $ajax = false)
    {
        if ($property) {
            $this->propertyPath = new PropertyPath($property);
        }

        parent::__construct($class, $property, $choices, $qo);
    }

    protected function load(): void
    {
        if (!$this->ajax) {
            parent::load();
        }
    }

    public function getChoices()
    {
        $choices = parent::getChoices();

        $array = [];
        foreach ($choices as $value => $label) {
            $array[] = [
                'value' => $value,
                'label' => $label,
            ];
        }

        return $array;
    }

    /**
     * Get intersaction $choices to $ids.
     *
     * @return array $intersect
     */
    public function getIntersect(array $ids)
    {
        $intersect = [];

        if ($this->ajax) {
            foreach ($ids as $id) {
                $model = $this->getModel($id);

                if ($this->propertyPath) {
                    $label = PropertyAccess::createPropertyAccessor()->getValue($model, $this->propertyPath);
                } else {
                    $label = (string) $model;
                }

                $intersect[] = [
                    'value' => $id,
                    'label' => $label,
                ];
            }
        } else {
            foreach ($this->getChoices() as $choice) {
                if (in_array($choice->value, $ids)) {
                    $intersect[] = $choice;
                }
            }
        }

        return $intersect;
    }
}
