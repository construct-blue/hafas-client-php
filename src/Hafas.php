<?php

namespace HafasClient;

use Carbon\Carbon;
use DateTime;
use GuzzleHttp\Exception\GuzzleException;
use HafasClient\Helper\OperatorFilter;
use HafasClient\Parser\TripParser;
use HafasClient\Profile\Config;
use HafasClient\Request\JourneyMatchRequest;
use HafasClient\Response\JourneyMatchResponse;
use HafasClient\Response\StationBoardResponse;
use HafasClient\Response\LocMatchResponse;
use HafasClient\Response\JourneyDetailsResponse;
use HafasClient\Models\Trip;
use HafasClient\Response\LocGeoPosResponse;
use HafasClient\Helper\ProductFilter;

class Hafas
{
    private Request $request;
    private Config $config;

    /**
     * @param Config $config
     * @param Request $request
     */
    public function __construct(Config $config, Request $request)
    {
        $this->config = $config;
        $this->request = $request;
    }

    public static function createDB(): Hafas
    {
        $config = Config::fromFile(__DIR__ . "/../profiles/db/config.json");
        $request = Request::fromFile(__DIR__ . "/../profiles/db/request.json");
        return new Hafas($config, $request);
    }

    public static function createOeBB(): Hafas
    {
        $config = Config::fromFile(__DIR__ . "/../profiles/oebb/config.json");
        $request = Request::fromFile(__DIR__ . "/../profiles/oebb/request.json");
        return new Hafas($config, $request);
    }


    /**
     * @throws GuzzleException|Exception\InvalidHafasResponse
     * @throws Exception\ProductNotFoundException|Exception\InvalidFilterException
     * @todo parse stopovers
     * @todo set language in request
     * @todo support remarks, hints, warnings
     * @todo filter by direction
     */
    public function getDepartures(
        int $lid,
        Carbon $timestamp,
        int $maxJourneys = 5,
        int $duration = -1,
        ProductFilter $filter = null,
    ): ?array {
        if ($filter === null) {
            //true is default for all
            $filter = new ProductFilter();
        }

        $data = [
            'req' => [
                'type' => 'DEP',
                'stbLoc' => [
                    'lid' => 'A=1@L=' . $lid . '@',
                ],
                'dirLoc' => null,
                //[ //direction, not required
                //                'lid' => '',
                //],
                'maxJny' => $maxJourneys,
                'date' => $timestamp->format('Ymd'),
                'time' => $timestamp->format('His'),
                'dur' => $duration,
                'jnyFltrL' => [$filter->filter()]
            ],
            'meth' => 'StationBoard'
        ];

        return (new StationBoardResponse($this->request->request($this->config, $data)))->parse();
    }

    /**
     * @param int $lid
     * @param Carbon $timestamp
     * @param int $maxJourneys
     * @param int $duration
     * @param ProductFilter|null $filter
     *
     * @return array|null
     * @throws Exception\InvalidFilterException
     * @throws Exception\InvalidHafasResponse
     * @throws Exception\ProductNotFoundException
     * @throws GuzzleException
     * @todo parse stopovers
     * @todo set language in request
     * @todo support remarks, hints, warnings
     * @todo filter by direction
     */
    public function getArrivals(
        int $lid,
        Carbon $timestamp,
        int $maxJourneys = 5,
        int $duration = -1,
        ProductFilter $filter = null,
    ): ?array {
        if ($filter === null) {
            //true is default for all
            $filter = new ProductFilter();
        }

        $data = [
            'req' => [
                'type' => 'ARR',
                'stbLoc' => [
                    'lid' => 'A=1@L=' . $lid . '@',
                ],
                'dirLoc' => null,
                //[ //direction, not required
                //                'lid' => '',
                //],
                'maxJny' => $maxJourneys,
                'date' => $timestamp->format('Ymd'),
                'time' => $timestamp->format('His'),
                'dur' => $duration,
                'jnyFltrL' => [$filter->filter()]
            ],
            'meth' => 'StationBoard'
        ];

        return (new StationBoardResponse($this->request->request($this->config, $data)))->parse();
    }

    /**
     * @param string $query
     * @param string $type 'S' = stations, 'ALL' stations and addresses
     *
     * @return array|null
     * @throws Exception\InvalidHafasResponse
     * @throws GuzzleException
     */
    public function getLocation(
        string $query,
        string $type = 'S'
    ): ?array {
        $data = [
            'req' => [
                'input' => [
                    'field' => 'S',
                    'loc' => [
                        'name' => $query,
                        'type' => $type
                    ]
                ]
            ],
            'meth' => 'LocMatch'
        ];

        return (new LocMatchResponse($this->request->request($this->config, $data)))->parse();
    }

    /**
     * @throws GuzzleException
     * @throws Exception\InvalidHafasResponse
     */
    public function getJourney(string $journeyId): ?Trip
    {
        $data = [
            'req' => [
                'jid' => $journeyId
            ],
            'meth' => 'JourneyDetails'
        ];
        return (new JourneyDetailsResponse(new TripParser($this->config)))->parse(
            $this->request->request($this->config, $data)
        );
    }

    /**
     * @throws GuzzleException
     * @throws Exception\InvalidHafasResponse
     */
    public function getNearby(float $latitude, float $longitude, $limit = 8): array
    {
        $data = [
            'req' => [
                "ring" => [
                    "cCrd" => [
                        "x" => $longitude * 1000000,
                        "y" => $latitude * 1000000
                    ],
                    "maxDist" => -1,
                    "minDist" => 0
                ],
                "locFltrL" => [
                    [
                        "type" => "PROD",
                        "mode" => "INC",
                        "value" => "1023"
                    ]
                ],
                "getPOIs" => false,
                "getStops" => true,
                "maxLoc" => $limit
            ],
            'cfg' => [
                'polyEnc' => 'GPA',
                'rtMode' => 'HYBRID',
            ],
            'meth' => 'LocGeoPos'
        ];

        return (new LocGeoPosResponse($this->request->request($this->config, $data)))->parse();
    }

    /**
     * @param JourneyMatchRequest $request
     * @return Trip[]
     * @throws Exception\InvalidHafasResponse
     * @throws GuzzleException
     */
    public function tripsByName(JourneyMatchRequest $request): array
    {
        return (new JourneyMatchResponse(new TripParser($this->config)))->parse(
            $this->request->request($this->config, $request->jsonSerialize()),
            $request
        );
    }

    public function trip(string $id): Trip
    {
        $data = [
            'req' => [
                'jid' => $id
            ],
            'meth' => 'JourneyDetails'
        ];
        return (new JourneyDetailsResponse(new TripParser($this->config)))->parse(
            $this->request->request($this->config, $data)
        );
    }

    /**
     * @param string $query
     * @param DateTime|null $fromWhen
     * @param DateTime|null $untilWhen
     * @param ProductFilter|null $productFilter
     * @param OperatorFilter|null $operatorFilter
     * @return Trip[]
     * @throws Exception\InvalidHafasResponse
     * @throws GuzzleException
     */
    public function searchTrips(
        string $query,
        DateTime $fromWhen = null,
        DateTime $untilWhen = null,
        ProductFilter $productFilter = null,
        OperatorFilter $operatorFilter = null
    ): array {
        $journeyMatchRequest = new JourneyMatchRequest($query, false);

        if ($productFilter) {
            $journeyMatchRequest->setProductFilter($productFilter);
        }

        if ($operatorFilter) {
            $journeyMatchRequest->setOperatorFilter($operatorFilter);
        }

        if ($fromWhen) {
            $journeyMatchRequest->setFromWhen($fromWhen);
        }
        if ($untilWhen) {
            $journeyMatchRequest->setUntilWhen($untilWhen);
        }

        return $this->tripsByName($journeyMatchRequest);
    }
}