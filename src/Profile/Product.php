<?php

declare(strict_types=1);

namespace HafasClient\Profile;

class Product
{

    /**
     * @param string $id
     * @param int[] $bitmasks
     * @param string $name
     * @param string $short
     * @param bool $default
     */
    public function __construct(
        private string $id,
        private array  $bitmasks,
        private string $name,
        private string $short,
        private bool   $default
    )
    {
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return array
     */
    public function getBitmasks(): array
    {
        return $this->bitmasks;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getShort(): string
    {
        return $this->short;
    }

    /**
     * @return bool
     */
    public function isDefault(): bool
    {
        return $this->default;
    }
}
