<?php

namespace HafasClient\Models;

use DateTime;
use JsonSerializable;

/**
 * @package HafasClient\Models
 */
readonly class Trip implements JsonSerializable
{
    /**
     * @param string $id
     * @param string|null $direction
     * @param DateTime|null $date
     * @param Line|null $line
     * @param Stopover[] $stopovers
     * @param Remark[] $remarks
     */
    public function __construct(
        public string    $id,
        public ?string   $direction = null,
        public ?DateTime $date = null,
        public ?Line     $line = null,
        public array     $stopovers = [],
        public array     $remarks = []
    )
    {
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