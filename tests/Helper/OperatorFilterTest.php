<?php

declare(strict_types=1);

namespace HafasClientTest\Helper;

use HafasClient\Helper\OperatorFilter;
use PHPUnit\Framework\TestCase;

class OperatorFilterTest extends TestCase
{
    public function testShouldBuildCommaSeperatedListForHafas()
    {
        $filter = new OperatorFilter('DB Fernverkehr AG', 'Österreichische Bundesbahnen');
        self::assertEquals([
            'type' => 'PROD',
            'mode' => 'INC',
            'value'=> 'DB Fernverkehr AG,Österreichische Bundesbahnen'
        ], $filter->filter());
    }
}
