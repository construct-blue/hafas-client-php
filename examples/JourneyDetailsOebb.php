<?php

require_once '../vendor/autoload.php';

\HafasClient\Hafas::$profile = 'oebb';

$data = \HafasClient\Hafas::searchTrips('RJ 79', new DateTime('today 00:00'), new DateTime('today 23:00'));
$data = \HafasClient\Hafas::trip($data[0]['id']);
file_put_contents(__DIR__ . '/data.json', json_encode($data));
