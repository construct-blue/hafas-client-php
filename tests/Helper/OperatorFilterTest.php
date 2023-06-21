<?php

declare(strict_types=1);

namespace HafasClientTest\Helper;

use HafasClient\Helper\OperatorFilter;
use HafasClient\Profile\Config;
use PHPUnit\Framework\TestCase;

class OperatorFilterTest extends TestCase
{
    public function testShouldBuildCommaSeperatedListForHafas()
    {
        $config = Config::fromFile(__DIR__ . '/../config/config.json');

        $filter = new OperatorFilter('dbfern', 'oebb');
        self::assertEquals([
            'type' => 'OP',
            'mode' => 'INC',
            'value'=> 'DB Fernverkehr AG,Österreichische Bundesbahnen'
        ], $filter->filter($config));
    }
}
