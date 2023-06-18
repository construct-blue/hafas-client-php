<?php

namespace HafasClient\Models;

/**
 * @package HafasClient\Models
 * @todo    make readonly
 */
class Remark implements \JsonSerializable
{

    public ?string $type;
    public ?string $code;
    public ?int $prio;
    public ?string $message;

    public function __construct(
        string $type = null,
        string $code = null,
        int $prio = null,
        string $message = null
    ) {
        $this->type = $type;
        $this->code = $code;
        $this->prio = $prio;
        $this->message = $message;
    }

    public function jsonSerialize(): array
    {
        return [
            'type' => $this->type,
            'code' => $this->code,
            'prio' => $this->prio,
            'message' => $this->message
        ];
    }

    /**
     * @return string
     * @todo
     */
    public function __toString(): string
    {
        return json_encode($this);
    }
}