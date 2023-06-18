<?php

namespace HafasClient\Models;

/**
 * @package HafasClient\Models
 * @todo    make readonly
 */
class Operator implements \JsonSerializable
{

    public ?string $id;
    public ?string $name;

    public function __construct(string $id = null, string $name = null)
    {
        $this->id = $id;
        $this->name = $name;
    }

    public function jsonSerialize(): mixed
    {
        return [
            'type' => 'operator',
            'id' => $this->id,
            'name' => $this->name,
        ];
    }

    public function __toString(): string
    {
        return json_encode($this);
    }
}