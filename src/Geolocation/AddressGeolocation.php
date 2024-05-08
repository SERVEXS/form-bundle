<?php

/*
 * This file is part of the GenemuFormBundle package.
 *
 * (c) Olivier Chauvel <olivier@generation-multiple.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Genemu\Bundle\FormBundle\Geolocation;

use Serializable;

/**
 * @author Olivier Chauvel <olivier@generation-multiple.com>
 */
class AddressGeolocation implements Serializable
{
    public function __construct(
        private $address,
        private $latitude = null,
        private $longitude = null,
        private $locality = null,
        private $country = null
    ) {
    }

    public function getAddress()
    {
        return $this->address;
    }

    public function getLatitude()
    {
        return $this->latitude;
    }

    public function getLongitude()
    {
        return $this->longitude;
    }

    public function getLocality()
    {
        return $this->locality;
    }

    public function getCountry()
    {
        return $this->country;
    }

    public function serialize()
    {
        return serialize([
            'address' => $this->address,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'locality' => $this->locality,
            'country' => $this->country,
        ]);
    }

    public function unserialize($serialized): void
    {
        $data = unserialize($serialized);

        $this->address = $data['address'] ?: null;
        $this->latitude = $data['latitude'] ?: null;
        $this->longitude = $data['longitude'] ?: null;
        $this->locality = $data['locality'] ?: null;
        $this->country = $data['country'] ?: null;
    }
}
