<?php

/*
 * This file is part of the Genemu package.
 *
 * (c) Olivier Chauvel <olivier@generation-multiple.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Genemu\Bundle\FormBundle\Gd\Filter;

use Genemu\Bundle\FormBundle\Gd\Gd;

/**
 * @author Olivier Chauvel <olivier@generation-multiple.com>
 */
class Strip extends Gd implements Filter
{
    protected $colors;

    /**
     * Construct.
     *
     * @param int $nb
     */
    public function __construct(array $colors, protected $nb = 15)
    {
        $this->colors = $colors;
    }

    public function apply()
    {
        $colors = $this->allocateColors($this->colors);

        $nbColor = count($colors) - 1;

        for ($i = 0; $i < $this->nb; ++$i) {
            $x = random_int(0, $this->width);
            $y = random_int(0, $this->height);

            $x2 = $x + random_int(-$this->width / 3, $this->width / 3);
            $y2 = $y + random_int(-$this->height / 3, $this->height / 3);

            $color = $colors[random_int(0, $nbColor)];

            imageline($this->resource, $x, $y, $x2, $y2, $color);
        }

        return $this->resource;
    }
}
