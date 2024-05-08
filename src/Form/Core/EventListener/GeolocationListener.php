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

use Genemu\Bundle\FormBundle\Geolocation\AddressGeolocation;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * GeoListener.
 *
 * @author Olivier Chauvel <olivier@generation-multiple.com>
 */
class GeolocationListener implements EventSubscriberInterface
{
    public function onBind(FormEvent $event): void
    {
        $data = $event->getData();

        if (empty($data)) {
            return;
        }

        $address = $data['address'];
        $latitude = $data['latitude'] ?? null;
        $longitude = $data['longitude'] ?? null;
        $locality = $data['locality'] ?? null;
        $country = $data['country'] ?? null;

        $geo = new AddressGeolocation($address, $latitude, $longitude, $locality, $country);

        $event->setData($geo);
    }

    public static function getSubscribedEvents(): array
    {
        return [FormEvents::PRE_SET_DATA => 'onBind'];
    }
}
