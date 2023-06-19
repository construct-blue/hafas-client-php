<?php

declare(strict_types=1);

namespace HafasClientTest\Response;

use HafasClient\Parser\TripParser;
use HafasClient\Response\JourneyDetailsResponse;
use PHPUnit\Framework\TestCase;

class JourneyDetailsResponseTest extends TestCase
{
    public function testParse()
    {
        $rawResponse = json_decode(file_get_contents(
            __DIR__ . '/../raw-responses/JourneyDetails-ICE28-running-with-delay.json'
        ));
        $response = new JourneyDetailsResponse(new TripParser());
        $journey = $response->parse($rawResponse);
        self::assertEquals('ICE 28', $journey->line->name);
        self::assertEquals('28', $journey->line->number);
        self::assertEquals('2023-06-18', $journey->date->format('Y-m-d'));
        self::assertEquals('Passau Hbf', $journey->direction);
        self::assertEquals('8A-B', $journey->stopovers[0]->departurePlatform);
        self::assertEquals('09:13:00', $journey->stopovers[0]->departure->format('H:i:s'));
        self::assertNull($journey->stopovers[0]->arrival);
        self::assertEquals('Wien Hbf', $journey->stopovers[0]->stop->name);
        self::assertEquals('Wien Meidling', $journey->stopovers[1]->stop->name);
        self::assertTrue($journey->stopovers[1]->reported);

        self::assertNull($journey->stopovers[16]->delay);
        self::assertEquals('18:46:00', $journey->stopovers[16]->arrival->format('H:i:s'));
        self::assertEquals('18:46:00', $journey->stopovers[16]->plannedArrival->format('H:i:s'));
        self::assertEquals('18:48:00', $journey->stopovers[16]->departure->format('H:i:s'));
        self::assertEquals('18:48:00', $journey->stopovers[16]->plannedDeparture->format('H:i:s'));


        self::assertEquals(1320, $journey->stopovers[17]->delay);
        self::assertEquals(1320, $journey->stopovers[18]->delay);
        self::assertEquals('19:30:00', $journey->stopovers[18]->arrival->format('H:i:s'));
        self::assertEquals('19:09:00', $journey->stopovers[18]->plannedArrival->format('H:i:s'));
        self::assertEquals('19:32:00', $journey->stopovers[18]->departure->format('H:i:s'));
        self::assertEquals('19:10:00', $journey->stopovers[18]->plannedDeparture->format('H:i:s'));
        self::assertTrue($journey->stopovers[3]->reported);
        self::assertTrue($journey->stopovers[5]->reported);
        self::assertTrue($journey->stopovers[16]->reported);
        self::assertTrue($journey->stopovers[18]->reported);
        self::assertFalse($journey->stopovers[19]->reported);
    }
}
