<?php

/*
 * This file is part of the GenemuFormBundle package.
 *
 * (c) Olivier Chauvel <olivier@generation-multiple.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Genemu\Bundle\FormBundle\Form\Core\EventListener;

use Genemu\Bundle\FormBundle\Gd\File\Image;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\File\File;

/**
 * Adds a protocol to a URL if it doesn't already have one.
 *
 * @author Bernhard Schussek <bernhard.schussek@symfony-project.com>
 * @author Olivier Chauvel <olivier@generation-multiple.com>
 */
class FileListener implements EventSubscriberInterface
{
    public function __construct(protected string $rootDir, protected bool $multiple = false)
    {
    }

    public function onBind(FormEvent $event): void
    {
        $data = $event->getData();

        if (empty($data)) {
            return;
        }

        if ($this->multiple) {
            $paths = explode(',', (string) $data);
            $return = [];

            foreach ($paths as $path) {
                if ($handle = $this->getHandleToPath($path)) {
                    $return[] = $handle;
                }
            }
        } else {
            if ($handle = $this->getHandleToPath($data)) {
                $return = $handle;
            }
        }

        $event->setData($return);
    }

    /**
     * Get Handle to Path.
     *
     * @param string $path
     *
     * @return File
     */
    private function getHandleToPath($path)
    {
        $path = $this->rootDir . '/' . $this->stripQueryString($path);

        if (is_file($path)) {
            $handle = new File($path);

            if (str_contains((string) $handle->getMimeType(), 'image')) {
                $handle = new Image($handle->getPathname());
            }

            return $handle;
        }

        return null;
    }

    /**
     * Delete info after `?`.
     *
     * @param string $file
     *
     * @return string
     */
    private function stripQueryString($file)
    {
        if (false !== ($pos = strpos($file, '?'))) {
            $file = substr($file, 0, $pos);
        }

        return $file;
    }

    public static function getSubscribedEvents(): array
    {
        return [FormEvents::PRE_SET_DATA => 'onBind'];
    }
}
