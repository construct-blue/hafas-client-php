<?php

namespace HafasClient;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use stdClass;

abstract class Request {
    /**
     * @param array  $svcReqL
     * @param string $userAgent
     *
     * @return stdClass
     * @throws GuzzleException
     */
    public static function request(array $svcReqL, string $userAgent = 'hafas-client-php'): stdClass {
        $profile = Hafas::$profile;

        $client = new Client();
        $request = json_decode(file_get_contents(__DIR__ . "/../profiles/$profile/request.json"), true);
        $config = json_decode(file_get_contents(__DIR__ . "/../profiles/$profile/config.json"), true);

        $requestBody = [
            'lang' => $config['defaultLanguage'],
            'svcReqL' => [$svcReqL],
            'client' => $request['client'],
            'ver' => $request['ver'],
            'auth' => $request['auth'],
        ];

        if (isset($request['ext'])) {
            $requestBody['ext'] = $request['ext'];
        }

        $requestBody = json_encode($requestBody);

        $query = [];
        if ($config['addChecksum']) {
            $query['checksum'] = self::getMac($requestBody, hex2bin($config['salt']));
        }

        if ($config['addMicMac']) {
            $query['mic'] = self::getMic($requestBody);
            $query['mac'] = self::getMac($requestBody, hex2bin($config['salt']));
        }

        $response = $client->post($config['endpoint'] . '?' . http_build_query($query), [
            'body'    => $requestBody,
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept-Encoding' => 'gzip, br, deflate',
                'User-Agent'   => $userAgent . uniqid(' '),
                'connection'   => 'keep-alive',
            ]

        ]);
        return json_decode($response->getBody()->getContents());
    }

    private static function getMic(string $requestBody): string {
        return md5($requestBody);
    }

    private static function getMac(string $requestBody, string $salt): string {
        return md5($requestBody . $salt);
    }
}