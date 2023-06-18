<?php

namespace HafasClient\Response;

use stdClass;
use HafasClient\Exception\InvalidHafasResponse;
use HafasClient\Models\Journey;
use HafasClient\Models\Line;
use HafasClient\Models\Operator;
use HafasClient\Models\Stopover;
use HafasClient\Models\Stop;
use HafasClient\Models\Location;
use HafasClient\Helper\Time;
use HafasClient\Models\Remark;

class JourneyDetailsResponse
{

    private stdClass $rawResponse;

    /**
     * @throws InvalidHafasResponse
     */
    public function __construct(stdClass $rawResponse)
    {
        $this->rawResponse = $rawResponse;
        if (!isset($rawResponse->svcResL[0]->res->journey)) {
            throw new InvalidHafasResponse();
        }
    }

    public function parse(): Journey
    {
        $rawJourney = $this->rawResponse->svcResL[0]->res->journey;
        $rawCommon = $this->rawResponse->svcResL[0]->res->common;
        $rawLine = $rawCommon->prodL[$rawJourney->prodX];
        $rawLineOperator = $rawCommon->opL[$rawLine->oprX];

        $stopovers = [];
        foreach ($rawJourney->stopL as $rawStop) {
            $rawLoc = $rawCommon->locL[$rawStop->locX];
            $plannedArrival = isset($rawStop->aTimeS) ? Time::parseDatetime($rawJourney->date, $rawStop->aTimeS) : null;
            $arrival = isset($rawStop->aTimeR) ? Time::parseDatetime(
                $rawJourney->date,
                $rawStop->aTimeR
            ) : $plannedArrival;
            $plannedDeparture = isset($rawStop->dTimeS) ? Time::parseDatetime(
                $rawJourney->date,
                $rawStop->dTimeS
            ) : null;
            $departure = isset($rawStop->dTimeR) ? Time::parseDatetime(
                $rawJourney->date,
                $rawStop->dTimeR
            ) : $plannedDeparture;

            $departureDelay = null;
            if ($plannedDeparture < $departure) {
                $departureDelay = $departure->diffInMinutes($plannedDeparture);
            }
            $arrivalDelay = null;
            if ($plannedArrival < $arrival) {
                $arrivalDelay = $arrival->diffInMinutes($plannedArrival);
            }

            $arrivalPlatformPlanned = $rawStop?->aPlatfS ?? $rawStop?->aPltfS?->txt ?? null;
            $arrivalPlatform = $rawStop?->aPlatfR ?? $rawStop?->aPltfR?->txt ?? $arrivalPlatformPlanned;
            $departurePlatformPlanned = $rawStop?->dPlatfS ?? $rawStop?->dPltfS?->txt ?? null;
            $departurePlatform = $rawStop?->dPlatfR ?? $rawStop?->dPltfR?->txt ?? $departurePlatformPlanned;
            $stopovers[] = new Stopover(
                stop: new Stop(
                    id: $rawLoc?->extId,
                    name: $rawLoc?->name,
                    location: new Location(
                        latitude: $rawLoc?->crd?->y / 1000000,
                        longitude: $rawLoc?->crd?->x / 1000000,
                        altitude: $rawLoc?->crd?->z ?? null
                    )
                ),
                index: $rawStop?->idx,
                plannedArrival: $plannedArrival,
                arrival: $arrival,
                arrivalPlatform: $arrivalPlatform,
                plannedDeparture: $plannedDeparture,
                departure: $departure,
                departurePlatform: $departurePlatform,
                isCancelled: isset($rawStop?->aCncl) || isset($rawStop?->dCnl),
                delay: $departureDelay ?? $arrivalDelay,
                reported: ($rawStop?->dProgType ?? null) === 'REPORTED'
            );
        }

        $remarks = [];
        foreach ($rawJourney->msgL ?? [] as $message) {
            $rawMessage = $rawCommon->remL[$message->remX];

            $remarks[] = new Remark(
                type: $rawMessage?->type ?? null,
                code: $rawMessage?->code ?? null,
                prio: $rawMessage?->prio ?? null,
                message: $rawMessage?->txtN ?? null,
            );
        }

        return new Journey(
            journeyId: $rawJourney?->jid ?? null,
            direction: $rawJourney?->dirTxt ?? null,
            date: Time::parseDate($rawJourney->date),
            line: new Line(
                id: '', //TODO
                name: $rawLine?->name ?? null,
                category: $rawLine?->prodCtx?->catOut ?? null,
                number: $rawLine?->number ?? null,
                mode: '',   //TODO
                product: '',//TODO
                operator: new Operator(
                    id: $rawLineOperator?->name ?? null, //TODO: where from?
                    name: $rawLineOperator?->name ?? null
                )
            ),
            stopovers: $stopovers,
            remarks: $remarks,
        );
    }
}