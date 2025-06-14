<?php

/*
 * This file is part of the GenemuFormBundle package.
 *
 * (c) Olivier Chauvel <olivier@generation-multiple.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Genemu\Bundle\FormBundle\Form\JQuery\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

/**
 * @author Bilal Amarni <bilal.amarni@gmail.com>
 */
class ArrayToStringTransformer implements DataTransformerInterface
{
    public function transform(mixed $value)
    {
        if (null === $value || !is_array($value)) {
            return '';
        }

        return implode(',', $value);
    }

    public function reverseTransform($string)
    {
        if (is_array($string)) {
            return $string;
        }

        return explode(',', (string) $string);
    }
}
