<?php

namespace HafasClient\Models;

use Carbon\Carbon;

/**
 * @package HafasClient\Models
 * @todo    make readonly
 */
class Stopover implements \JsonSerializable
{

    public Stop $stop;
    public ?int $index;
    public ?Carbon $plannedArrival;
    public ?Carbon $arrival;
    public ?string $arrivalPlatform;
    public ?Carbon $plannedDeparture;
    public ?Carbon $departure;
    public ?string $departurePlatform;
    public ?bool $isCancelled;
    public ?int $delay;
    public ?bool $reported;

    public function __construct(
        Stop $stop,
        int $index = null,
        Carbon $plannedArrival = null,
        Carbon $arrival = null,
        string $arrivalPlatform = null,
        Carbon $plannedDeparture = null,
        Carbon $departure = null,
        string $departurePlatform = null,
        bool $isCancelled = null,
        int $delay = null,
        bool $reported = null
    ) {
        $this->stop = $stop;
        $this->index = $index;
        $this->plannedArrival = $plannedArrival;
        $this->arrival = $arrival;
        $this->arrivalPlatform = $arrivalPlatform;
        $this->plannedDeparture = $plannedDeparture;
        $this->departure = $departure;
        $this->departurePlatform = $departurePlatform;
        $this->isCancelled = $isCancelled;
        $this->delay = $delay;
        $this->reported = $reported;
    }

    public function jsonSerialize(): mixed
    {
        return [
            'type' => 'stopover',
            'stop' => isset($this->stop) ? (string)$this->stop : null,
            'index' => $this->index,
            'plannedArrival' => $this->plannedArrival,
            'predictedArrival' => $this->arrival,
            'arrivalPlatform' => $this->arrivalPlatform,
            'plannedDeparture' => $this->plannedDeparture,
            'predictedDeparture' => $this->departure,
            'departurePlatform' => $this->departurePlatform,
            'isCancelled' => $this->isCancelled,
            'delay' => $this->delay,
            'reported' => $this->reported
        ];
    }

    public function __toString(): string
    {
        return json_encode($this);
    }
}
