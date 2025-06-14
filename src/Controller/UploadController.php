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

readonly class UploadController
{
    public function __construct(private ParameterBagInterface $parameterBag)
    {
    }

    public function uploadAction(Request $request): JsonResponse
    {
        $handle = $request->files->get('Filedata');

        $folder = $this->parameterBag->get('genemu.form.file.folder');
        $uploadDir = $this->parameterBag->get('genemu.form.file.upload_dir');
        $name = uniqid() . 'Controller' . $handle->guessExtension();

        $json = [];
        if ($handle = $handle->move($uploadDir, $name)) {
            $json = [
                'result' => '1',
                'thumbnail' => [],
                'image' => [],
                'file' => '',
            ];

            if (str_contains((string) $handle->getMimeType(), 'image')) {
                $handle = new Image($handle->getPathname());
                $thumbnail = $handle;

                if ($this->parameterBag->has('genemu.form.image.thumbnails')) {
                    $thumbnails = $this->parameterBag->get('genemu.form.image.thumbnails');

                    foreach ($thumbnails as $name => $thumbnail) {
                        $handle->createThumbnail($name, $thumbnail[0], $thumbnail[1]);
                    }

                    if (0 < count($thumbnails)) {
                        $selected = key(reset($thumbnails));
                        if ($this->parameterBag->has('genemu.form.image.selected')) {
                            $selected = $this->parameterBag->get('genemu.form.image.selected');
                        }

                        $thumbnail = $handle->getThumbnail($selected);
                    }
                }

                $json = array_replace($json, [
                    'thumbnail' => [
                        'file' => $folder . '/' . $thumbnail->getFilename() . '?' . time(),
                        'width' => $thumbnail->getWidth(),
                        'height' => $thumbnail->getHeight(),
                    ],
                    'image' => [
                        'width' => $handle->getWidth(),
                        'height' => $handle->getHeight(),
                    ],
                ]);
            }

            $json['file'] = $folder . '/' . $handle->getFilename() . '?' . time();
        } else {
            $json['result'] = '0';
        }

        return new JsonResponse($json);
    }
}
