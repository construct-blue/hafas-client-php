<?php

declare(strict_types=1);

namespace HafasClient\Profile;

class Config
{
    private string $userAgent;
    private string $endpoint;
    private ?string $salt;
    private string $defaultLanguage;
    private bool $addMicMac;
    private bool $addChecksum;
    private int $defaultTZOffset;

    private array $products;


    /**
     * @param string $endpoint
     * @param string|null $salt
     * @param string $defaultLanguage
     * @param bool $addMicMac
     * @param bool $addChecksum
     * @param int $defaultTZOffset
     */
    public function __construct(
        string $userAgent,
        string $endpoint,
        ?string $salt,
        string $defaultLanguage,
        bool $addMicMac,
        bool $addChecksum,
        int $defaultTZOffset,
        array $products
    ) {
        $this->userAgent = $userAgent;
        $this->endpoint = $endpoint;
        $this->salt = $salt;
        $this->defaultLanguage = $defaultLanguage;
        $this->addMicMac = $addMicMac;
        $this->addChecksum = $addChecksum;
        $this->defaultTZOffset = $defaultTZOffset;
        $this->products = $products;
    }

    public static function fromFile(string $filename): Config
    {
        $data = json_decode(file_get_contents($filename));
        return new Config(
            userAgent: $data->userAgent,
            endpoint: $data->endpoint,
            salt: $data->salt,
            defaultLanguage: $data->defaultLanguage,
            addMicMac: $data->addMicMac,
            addChecksum: $data->addChecksum,
            defaultTZOffset: $data->defaultTZOffset,
            products: $data->products ?? []
        );
    }

    /**
     * @return string
     */
    public function getUserAgent(): string
    {
        return $this->userAgent;
    }

    /**
     * @return string
     */
    public function getEndpoint(): string
    {
        return $this->endpoint;
    }

    /**
     * @return string|null
     */
    public function getSalt(): ?string
    {
        return $this->salt;
    }

    /**
     * @return string
     */
    public function getDefaultLanguage(): string
    {
        return $this->defaultLanguage;
    }

    /**
     * @return bool
     */
    public function isAddMicMac(): bool
    {
        return $this->addMicMac;
    }

    /**
     * @return bool
     */
    public function isAddChecksum(): bool
    {
        return $this->addChecksum;
    }

    /**
     * @return int
     */
    public function getDefaultTZOffset(): int
    {
        return $this->defaultTZOffset;
    }

    /**
     * @return array
     */
    public function getProducts(): array
    {
        return $this->products;
    }
}