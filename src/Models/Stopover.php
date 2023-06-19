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
    private ?int $arrivalDelay;
    private ?int $departureDelay;
    public ?bool $reported;
    public ?bool $border;

    public array $remarks;

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
        int $arrivalDelay = null,
        int $departureDelay = null,
        bool $reported = null,
        bool $border = null,
        array $remarks = []
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
        $this->arrivalDelay = $arrivalDelay;
        $this->departureDelay = $departureDelay;
        $this->reported = $reported;
        $this->border = $border;
        $this->remarks = $remarks;
    }

    public function jsonSerialize(): array
    {
        return [
            'type' => 'stopover',
            'stop' => $this->stop ?? null,
            'index' => $this->index,
            'plannedArrival' => $this->plannedArrival?->format(DATE_ATOM),
            'arrival' => $this->arrival?->format(DATE_ATOM),
            'arrivalPlatform' => $this->arrivalPlatform,
            'plannedDeparture' => $this->plannedDeparture?->format(DATE_ATOM),
            'departure' => $this->departure?->format(DATE_ATOM),
            'departurePlatform' => $this->departurePlatform,
            'isCancelled' => $this->isCancelled,
            'delay' => $this->delay,
            'arrivalDelay' => $this->arrivalDelay,
            'departureDelay' => $this->departureDelay,
            'reported' => $this->reported,
            'border' => $this->border,
            'remarks' => $this->remarks,
        ];
    }

    public function __toString(): string
    {
        return json_encode($this);
    }
}
