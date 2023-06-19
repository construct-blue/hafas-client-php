<?php

namespace HafasClient\Response;

use HafasClient\Exception\InvalidHafasResponse;
use HafasClient\Models\Trip;
use HafasClient\Parser\TripParser;
use stdClass;

class JourneyMatchResponse
{

    public function __construct(private TripParser $parser)
    {
    }

    /**
     * @return Trip[]
     * @throws InvalidHafasResponse
     */
    public function parse(stdClass $rawResponse): array
    {
        if (!isset($rawResponse->svcResL[0]->res->jnyL)) {
            throw new InvalidHafasResponse();
        }

        $rawCommon = $rawResponse->svcResL[0]->res->common;

        $journeys = [];
        foreach ($rawResponse->svcResL[0]->res->jnyL as $rawJourney) {
            $journeys[] = $this->parser->parse($rawCommon, $rawJourney);
        }
        return $journeys;
    }
}