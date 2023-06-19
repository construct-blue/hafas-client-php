<?php

namespace HafasClient\Response;

use HafasClient\Exception\InvalidHafasResponse;
use HafasClient\Models\Trip;
use HafasClient\Parser\TripParser;
use HafasClient\Request\JourneyMatchRequest;
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
    public function parse(stdClass $rawResponse, JourneyMatchRequest $request): array
    {
        if (!isset($rawResponse->svcResL[0]->res->jnyL)) {
            throw new InvalidHafasResponse();
        }

        $rawCommon = $rawResponse->svcResL[0]->res->common;

        $journeys = [];
        foreach ($rawResponse->svcResL[0]->res->jnyL as $rawJourney) {
            $trip = $this->parser->parse($rawCommon, $rawJourney);
            if ($request->getAdmin() && $trip->line->admin !== $request->getAdmin()) {
                continue;
            }
            $journeys[] = $trip;
        }
        return $journeys;
    }
}