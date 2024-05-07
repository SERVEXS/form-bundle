<?php

/*
 * This file is part of the Genemu package.
 *
 * (c) Olivier Chauvel <olivier@generation-multiple.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gd\Filter;

use Gd\Gd;

/**
 * @author Olivier Chauvel <olivier@generation-multiple.com>
 */
class GrayScale extends Gd implements Filter
{
    /**
     * {@inheritdoc}
     */
    public function apply()
    {
        imagefilter($this->resource, IMG_FILTER_GRAYSCALE);

        return $this->resource;
    }
}
