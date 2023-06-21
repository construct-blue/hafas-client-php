<?php

declare(strict_types=1);

namespace HafasClient\Profile;

use stdClass;

class Config
{
    private string $userAgent;
    private string $endpoint;
    private ?string $salt;
    private string $defaultLanguage;
    private bool $addMicMac;
    private bool $addChecksum;
    private int $defaultTZOffset;

    /** @var Product[] */
    private array $products;


    /**
     * @param string $userAgent
     * @param string $endpoint
     * @param string|null $salt
     * @param string $defaultLanguage
     * @param bool $addMicMac
     * @param bool $addChecksum
     * @param int $defaultTZOffset
     * @param Product[] $products
     */
    public function __construct(
        string  $userAgent,
        string  $endpoint,
        ?string $salt,
        string  $defaultLanguage,
        bool    $addMicMac,
        bool    $addChecksum,
        int     $defaultTZOffset,
        array   $products
    )
    {
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
            userAgent: $data->userAgent ?? 'hafas-php-client',
            endpoint: $data->endpoint,
            salt: (string)$data->salt ?? '',
            defaultLanguage: (string)$data->defaultLanguage ?? 'en',
            addMicMac: (bool)$data->addMicMac ?? false,
            addChecksum: (bool)$data->addChecksum ?? false,
            defaultTZOffset: (int)$data->defaultTZOffset ?? 0,
            products: array_map(
                fn(stdClass $product) => new Product(
                    (string)$product->id ?? '',
                    array_map('intval', $product->bitmasks),
                    (string)$product->name ?? '',
                    (string)$product->short ?? '',
                    (bool)$product->default
                ),
                $data->products ?? [])
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
     * @return Product[]
     */
    public function getProducts(): array
    {
        return $this->products;
    }
}