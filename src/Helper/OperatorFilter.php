<?php

declare(strict_types=1);

namespace HafasClient\Helper;

class OperatorFilter
{
    private array $filter;


    public function __construct(string ...$operator)
    {
        $this->filter = $operator;
    }


    public function filter(): array
    {
        return [
            'type' => 'OP',
            'mode' => 'INC',
            'value' => implode(',', $this->filter)
        ];
    }
}