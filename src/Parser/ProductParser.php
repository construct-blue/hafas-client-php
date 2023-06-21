<?php

declare(strict_types=1);

namespace HafasClient\Parser;

use HafasClient\Models\Product;
use HafasClient\Profile\Config;

class ProductParser
{
    public function __construct(private readonly Config $config)
    {
    }

    /**
     * @param int $cls
     * @return Product[]
     */
    public function parse(int $cls): array
    {
        $result = [];
        foreach ($this->config->getProducts() as $product) {
            foreach ($product->bitmasks as $bitmask) {
                if ($cls & $bitmask) {
                    $result[] = $product;
                }
            }
        }
        return $result;
    }
}