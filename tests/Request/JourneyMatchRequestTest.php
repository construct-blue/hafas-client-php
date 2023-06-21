<?php

declare(strict_types=1);

namespace HafasClientTest\Request;

use DateTime;
use HafasClient\Helper\OperatorFilter;
use HafasClient\Helper\ProductFilter;
use HafasClient\Helper\Time;
use HafasClient\Profile\Config;
use HafasClient\Request\JourneyMatchRequest;
use PHPUnit\Framework\TestCase;

class JourneyMatchRequestTest extends TestCase
{
    public function testDefaultFilterSimpleSearch()
    {
        $config = Config::fromFile(__DIR__ . '/../config/config.json');

        $request = new JourneyMatchRequest('ICE 70', false);
        self::assertEquals([
            'cfg' => [
                'polyEnc' => 'GPA',
                'rtMode' => 'REALTIME',
            ],
            'meth' => 'JourneyMatch',
            'req' => [
                'input' => 'ICE 70',
                'onlyCR' => false,
                'jnyFltrL' => [
                    (new ProductFilter())->filter()
                ],
            ],
        ], $request->toArray($config));
    }

    public function testSearchWithTimeRange()
    {
        $config = Config::fromFile(__DIR__ . '/../config/config.json');

        $request = new JourneyMatchRequest('ICE 70', false);
        $fromWhen = new DateTime('today 00:00');
        $untilWhen = new DateTime('today 23:59');
        $request->setFromWhen(new DateTime('today 00:00'));
        $request->setUntilWhen(new DateTime('today 23:59'));
        self::assertEquals([
            'cfg' => [
                'polyEnc' => 'GPA',
                'rtMode' => 'REALTIME',
            ],
            'meth' => 'JourneyMatch',
            'req' => [
                'input' => 'ICE 70',
                'onlyCR' => false,
                'jnyFltrL' => [
                    (new ProductFilter())->filter()
                ],
                'dateB' => Time::formatDate($fromWhen),
                'timeB' => '000000',
                'dateE' => Time::formatDate($untilWhen),
                'timeE' => '235900',
            ],
        ], $request->toArray($config));
    }

    public function testSearchWithOperatorFilter()
    {
        $config = Config::fromFile(__DIR__ . '/../config/config.json');

        $request = new JourneyMatchRequest('ICE 70', false);
        $request->setOperatorFilter(new OperatorFilter('dbfern'));
        self::assertEquals([
            'cfg' => [
                'polyEnc' => 'GPA',
                'rtMode' => 'REALTIME',
            ],
            'meth' => 'JourneyMatch',
            'req' => [
                'input' => 'ICE 70',
                'onlyCR' => false,
                'jnyFltrL' => [
                    (new ProductFilter())->filter(),
                    (new OperatorFilter('dbfern'))->filter($config)
                ],
            ],
        ], $request->toArray($config));
    }
}
