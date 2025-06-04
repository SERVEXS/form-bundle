<?php

/*
 * This file is part of the GenemuFormBundle package.
 *
 * (c) Olivier Chauvel <olivier@generation-multiple.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Genemu\Bundle\FormBundle\Form\Doctrine\ChoiceList;

use Closure;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bridge\Doctrine\Form\ChoiceList\ORMQueryBuilderLoader;
use Symfony\Component\Form\ChoiceList\LazyChoiceList;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyPath;

/**
 * AjaxEntityChoiceList.
 *
 * @author Olivier Chauvel <olivier@generation-multiple.com>
 */
class AjaxEntityChoiceList extends LazyChoiceList
{
    private $propertyPath;

    private $classMetadata;

    /**
     * Constructs.
     *
     * @param string        $class
     * @param string        $property
     * @param QueryBuilder  $qb
     * @param array|Closure $choices
     * @param string        $groupBy
     * @param bool          $ajax
     */
    public function __construct(
        ObjectManager $em,
        $class,
        $property = null,
        $qb = null,
        $choices = null,
        $groupBy = null,
        private $ajax = false
    ) {
        $this->classMetadata = $em->getClassMetadata($class);

        if ($property) {
            $this->propertyPath = new PropertyPath($property);
        }

        $loader = $qb ? new ORMQueryBuilderLoader($qb) : null;

        parent::__construct($em, $class, $property, $loader, $choices, [], $groupBy);
    }

    protected function load(): void
    {
        if (!$this->ajax) {
            parent::load();
        }
    }

    public function getChoices(): array
    {
        $choices = $this->getRemainingViews();

        if (empty($choices)) {
            $choices = [];
        }

        $array = [];
        foreach ($choices as $choice) {
            $array[] = [
                'value' => $choice->value,
                'label' => $choice->label,
            ];
        }

        return $array;
    }

    public function getRemainingViews()
    {
        if ($this->ajax) {
            return [];
        }

        return parent::getRemainingViews();
    }

    public function getPreferredViews()
    {
        if ($this->ajax) {
            return [];
        }

        return parent::getPreferredViews();
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
            foreach ($this->getChoicesForValues($ids) as $entity) {
                $id = current($this->classMetadata->getIdentifierValues($entity));

                if ($this->propertyPath) {
                    $label = PropertyAccess::createPropertyAccessor()->getValue($entity, $this->propertyPath);
                } else {
                    $label = (string) $entity;
                }

                $intersect[] = [
                    'value' => $id,
                    'label' => $label,
                ];
            }
        } else {
            foreach ($this->getChoices() as $choice) {
                if (in_array($choice['value'], $ids)) {
                    $intersect[] = $choice;
                }
            }
        }

        return $intersect;
    }
}
