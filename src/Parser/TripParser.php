<?php

declare(strict_types=1);

namespace HafasClient\Parser;

use HafasClient\Helper\Time;
use HafasClient\Models\Trip;
use HafasClient\Models\Line;
use HafasClient\Models\Location;
use HafasClient\Models\Operator;
use HafasClient\Models\Remark;
use HafasClient\Models\Stop;
use HafasClient\Models\Stopover;
use stdClass;

class TripParser
{
    public function parse(stdClass $rawCommon, stdClass $rawJourney): Trip
    {
        $rawLine = $rawCommon->prodL[$rawJourney->prodX];
        $rawLineOperator = $rawCommon->opL[$rawLine->oprX];

        $stopovers = [];
        foreach ($rawJourney->stopL as $index => $rawStop) {
            $rawLoc = $rawCommon->locL[$rawStop->locX];
            $plannedArrival = isset($rawStop->aTimeS) ? Time::parseDatetime($rawJourney->date, $rawStop->aTimeS, (float)$rawStop->aTZOffset) : null;
            $arrival = isset($rawStop->aTimeR) ? Time::parseDatetime(
                $rawJourney->date,
                $rawStop->aTimeR,
                (float)$rawStop->aTZOffset
            ) : $plannedArrival;
            $plannedDeparture = isset($rawStop->dTimeS) ? Time::parseDatetime(
                $rawJourney->date,
                $rawStop->dTimeS,
                (float)$rawStop->dTZOffset
            ) : null;
            $departure = isset($rawStop->dTimeR) ? Time::parseDatetime(
                $rawJourney->date,
                $rawStop->dTimeR,
                (float)$rawStop->dTZOffset
            ) : $plannedDeparture;

            $departureDelay = null;
            if ($plannedDeparture < $departure) {
                $departureDelay = $departure->diffInSeconds($plannedDeparture);
            }
            $arrivalDelay = null;
            if ($plannedArrival < $arrival) {
                $arrivalDelay = $arrival->diffInSeconds($plannedArrival);
            }

            $arrivalPlatformPlanned = $rawStop?->aPlatfS ?? $rawStop?->aPltfS?->txt ?? null;
            $arrivalPlatform = $rawStop?->aPlatfR ?? $rawStop?->aPltfR?->txt ?? $arrivalPlatformPlanned;
            $departurePlatformPlanned = $rawStop?->dPlatfS ?? $rawStop?->dPltfS?->txt ?? null;
            $departurePlatform = $rawStop?->dPlatfR ?? $rawStop?->dPltfR?->txt ?? $departurePlatformPlanned;
            $stopovers[] = new Stopover(
                stop: new Stop(
                    id: $rawLoc?->extId ?? '',
                    name: $rawLoc?->name ?? '',
                    location: new Location(
                        latitude: $rawLoc?->crd?->y / 1000000,
                        longitude: $rawLoc?->crd?->x / 1000000,
                        altitude: $rawLoc?->crd?->z ?? null
                    )
                ),
                index: $rawStop?->idx ?? $index,
                plannedArrival: $plannedArrival,
                arrival: $arrival,
                arrivalPlatform: $arrivalPlatform,
                plannedDeparture: $plannedDeparture,
                departure: $departure,
                departurePlatform: $departurePlatform,
                isCancelled: isset($rawStop?->aCncl) || isset($rawStop?->dCnl),
                delay: $departureDelay ?? $arrivalDelay,
                arrivalDelay: $arrivalDelay,
                departureDelay: $departureDelay,
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

        return new Trip(
            id: $rawJourney?->jid ?? '',
            direction: $rawJourney?->dirTxt ?? null,
            date: Time::parseDate($rawJourney->date),
            line: new Line(
                id: $rawLine?->prodCtx?->lineId ?? $rawLine?->prodCtx?->matchId ?? '', //TODO
                name: $rawLine?->name ?? null,
                category: $rawLine?->prodCtx?->catOut ?? null,
                number: $rawLine?->number ?? null,
                mode: '',//TODO map to products $rawLine?->cls is bitmask of product
                product: '',//TODO
                operator: new Operator(
                    id: $rawLineOperator?->name ?? null, //TODO: where from?
                    name: $rawLineOperator?->name ?? null
                ),
                admin: $rawLine?->prodCtx?->admin ?? null,
            ),
            stopovers: $stopovers,
            remarks: $remarks,
        );
    }
}