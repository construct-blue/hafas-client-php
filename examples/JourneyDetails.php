<?php

require_once '../vendor/autoload.php';

$data = \HafasClient\Hafas::searchTrips('ICE 28', new DateTime('today 00:00'), new DateTime('today 23:00'));
$data = \HafasClient\Hafas::trip($data[0]['id']);
file_put_contents(__DIR__ . '/data.json', json_encode($data));
