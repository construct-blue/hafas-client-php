<?php

require_once '../vendor/autoload.php';

$latitude  = 52.376954;
$longitude = 9.741574;

$data = \HafasClient\Hafas::getNearby($latitude, $longitude);

echo "We found " . count($data) . " nearby stations:" . PHP_EOL;

foreach($data as $key => $stop) {
    echo strtr('[:index] :id // :name', [
            ':index' => $key + 1,
            ':id'    => $stop->id,
            ':name'  => $stop->name
        ]) . PHP_EOL;
}
