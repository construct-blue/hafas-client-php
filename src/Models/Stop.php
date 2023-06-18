<?php

namespace HafasClient\Models;

/**
 * @package HafasClient\Models
 * @todo    make readonly
 */
class Stop implements \JsonSerializable
{

    public string $id;
    public string $name;
    public ?Location $location;

    public function __construct(string $id, string $name, Location $location = null)
    {
        $this->id = $id;
        $this->name = $name;
        $this->location = $location;
    }

    public function jsonSerialize(): mixed
    {
        return [
            'type' => 'stop',
            'id' => $this->id,
            'name' => $this->name,
            'location' => isset($this->location) ? (string)$this->location : null
        ];
    }

    public function __toString(): string
    {
        return json_encode($this);
    }
}