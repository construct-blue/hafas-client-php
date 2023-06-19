<?php

namespace HafasClient\Models;

use Carbon\Carbon;

/**
 * @package HafasClient\Models
 * @todo    make readonly
 */
class Trip implements \JsonSerializable
{

    public string $id;
    public ?string $direction;
    public ?Carbon $date;
    public ?Line $line;
    /** @var Stopover[]|null */
    public ?array $stopovers;
    /** @var Remark[]|null  */
    public ?array $remarks;

    public function __construct(
        string $id,
        string $direction = null,
        Carbon $date = null,
        Line $line = null,
        array $stopovers = null,
        array $remarks = null
    ) {
        $this->id = $id;
        $this->direction = $direction;
        $this->date = $date;
        $this->line = $line;
        $this->stopovers = $stopovers;
        $this->remarks = $remarks;
    }

    public function jsonSerialize(): array
    {
        return [
            'type' => 'trip',
            'id' => $this->id,
            'date' => $this->date?->format('Y-m-d') ?? null,
            'line' => $this->line ?? null,
            'stopovers' => $this->stopovers,
            'remarks' => $this->remarks,
        ];
    }

    public function __toString(): string
    {
        return json_encode($this);
    }
}