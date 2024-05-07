<?php

/*
 * This file is part of the GenemuFormBundle package.
 *
 * (c) Olivier Chauvel <olivier@generation-multiple.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Genemu\Bundle\FormBundle\Controller;

use Genemu\Bundle\FormBundle\Gd\File\Image;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ImageController
{
    public function __construct(private readonly ParameterBagInterface $parameterBag)
    {
    }

    public function changeAction(Request $request): JsonResponse
    {
        $rootDir = rtrim($this->parameterBag->get('genemu.form.file.root_dir'), '/\\') . DIRECTORY_SEPARATOR;
        $folder = rtrim($this->parameterBag->get('genemu.form.file.folder'), '/\\');

        $file = $request->get('image');
        $handle = new Image($rootDir . $this->stripQueryString($file));

        switch ($request->get('filter')) {
            case 'rotate':
                $handle->addFilterRotate(90);

                break;
            case 'negative':
                $handle->addFilterNegative();

                break;
            case 'bw':
                $handle->addFilterBw();

                break;
            case 'sepia':
                $handle->addFilterSepia('#C68039');

                break;
            case 'crop':
                $x = $request->get('x');
                $y = $request->get('y');
                $w = $request->get('w');
                $h = $request->get('h');

                $handle->addFilterCrop($x, $y, $w, $h);

                break;
            case 'blur':
                $handle->addFilterBlur();

            default:
                break;
        }

        $handle->save();
        $thumbnail = $handle;

        if ($this->parameterBag->has('genemu.form.image.thumbnails')) {
            $thumbnails = $this->parameterBag->get('genemu.form.image.thumbnails');

            foreach ($thumbnails as $name => $thumbnail) {
                $handle->createThumbnail($name, $thumbnail[0], $thumbnail[1]);
            }

            $selected = key(reset($thumbnails));
            if ($this->parameterBag->has('genemu.form.image.selected')) {
                $selected = $this->parameterBag->get('genemu.form.image.selected');
            }

            $thumbnail = $handle->getThumbnail($selected);
        }

        $json = array(
            'result' => '1',
            'file' => $folder . '/' . $handle->getFilename() . '?' . time(),
            'thumbnail' => array(
                'file' => $folder . '/' . $thumbnail->getFilename() . '?' . time(),
                'width' => $thumbnail->getWidth(),
                'height' => $thumbnail->getHeight(),
            ),
            'image' => array(
                'width' => $handle->getWidth(),
                'height' => $handle->getHeight(),
            ),
        );

        return new JsonResponse($json);
    }

    /**
     * Delete info after `?`
     */
    private function stripQueryString(string $file): string
    {
        if (false !== ($pos = strpos($file, '?'))) {
            return substr($file, 0, $pos);
        }

        return $file;
    }
}
