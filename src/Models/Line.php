<?php

namespace HafasClient\Models;

/**
 * @package HafasClient\Models
 * @todo    make readonly
 */
class Line implements \JsonSerializable
{

    public string $id;
    public ?string $name;
    public ?string $category;
    public ?string $number;
    public ?string $mode;
    public ?string $product;
    public ?Operator $operator;
    public ?string $admin;

    public function __construct(
        string $id,
        string $name = null,
        string $category = null,
        string $number = null,
        string $mode = null,
        string $product = null,
        Operator $operator = null,
        string $admin = null
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->category = $category;
        $this->number = $number;
        $this->mode = $mode;
        $this->product = $product;
        $this->operator = $operator;
        $this->admin = $admin;
    }

    public function jsonSerialize(): mixed
    {
        return [
            'type' => 'line',
            'id' => $this->id,
            'name' => $this->name,
            'category' => $this->category,
            'number' => $this->number,
            'mode' => $this->mode,
            'product' => $this->product,
            'operator' => $this->operator ?? null,
            'admin' => $this->admin ?? null,
        ];
    }

    public function __toString(): string
    {
        return json_encode($this);
    }
}