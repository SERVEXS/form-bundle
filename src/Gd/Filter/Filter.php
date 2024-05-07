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

use Gd\GdInterface;

/**
 * @author Olivier Chauvel <olivier@generation-multiple.com>
 */
interface Filter extends GdInterface
{
    /**
     * Code execute to apply filter
     */
    public function apply();
}
