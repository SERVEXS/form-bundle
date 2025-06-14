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
class Distort extends Gd implements Filter
{
    public function apply()
    {
        $X = random_int(0, $this->width);
        $Y = random_int(0, $this->height);
        $Phase = random_int(0, 10);
        $Scale = 1.3 + random_int(0, 10000) / 30000;
        $Amp = 1 + random_int(0, 1000) / 1000;

        for ($x = 0; $x < $this->width; ++$x) {
            for ($y = 0; $y < $this->height; ++$y) {
                $Vx = $x - $X;
                $Vy = $y - $Y;
                $Vn = sqrt($Vx * $Vx + $Vy * $Vy);

                if (0 != $Vn) {
                    $Vn2 = $Vn + 4 * sin($Vn / 8);
                    $nX = $X + ($Vx * $Vn2 / $Vn);
                    $nY = $Y + ($Vy * $Vn2 / $Vn);
                } else {
                    $nX = $X;
                    $nY = $Y;
                }
                $nY = $nY + $Scale * sin($Phase + $nX * 0.2);

                $p = $this->bilinearInterpolate(
                    $nX - floor($nX),
                    $nY - floor($nY),
                    $this->getColor(floor($nX), floor($nY)),
                    $this->getColor(ceil($nX), floor($nY)),
                    $this->getColor(floor($nX), ceil($nY)),
                    $this->getColor(ceil($nX), ceil($nY))
                );

                if (0 === $p) {
                    $p = 0xFFFFFF;
                }

                $color = $this->getColor($x, $y);

                imagesetpixel($this->resource, $x, $y, $p);
            }
        }

        return $this->resource;
    }
}
