<?php

namespace HafasClient\Models;

use JsonSerializable;

/**
 * @package HafasClient\Models
 */
readonly class Operator implements JsonSerializable
{
    public function __construct(
        public ?string $id = null,
        public ?string $name = null,
        public ?string $admin = null
    )
    {
    }

    public function jsonSerialize(): array
    {
        return [
            'type' => 'operator',
            'id' => $this->id,
            'name' => $this->name,
            'admin' => $this->admin
        ];
    }

    public function __toString(): string
    {
        return json_encode($this);
    }
}