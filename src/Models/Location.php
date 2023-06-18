<?php

namespace HafasClient\Models;

/**
 * Coordinates are in the World Geodetic System (WGS84)
 * @package HafasClient\Models
 * @todo    make readonly
 */
class Location implements \JsonSerializable {

    public float  $latitude;
    public float  $longitude;
    public ?float $altitude;

    public function __construct(float $latitude, float $longitude, ?float $altitude) {
        $this->latitude  = $latitude;
        $this->longitude = $longitude;
        $this->altitude  = $altitude;
    }

    public function jsonSerialize(): mixed
    {
        return [
            'type'      => 'location',
            'latitude'  => $this->latitude,
            'longitude' => $this->longitude,
            'altitude'  => $this->altitude,
        ];
    }

    public function __toString(): string {
        return json_encode($this);
    }
}