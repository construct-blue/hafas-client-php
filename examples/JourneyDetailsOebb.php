<?php

require_once '../vendor/autoload.php';

$data = \HafasClient\Hafas::createOeBB()->searchTrips('RJ 79', new DateTime('today 00:00'), new DateTime('today 23:00'));
$data = \HafasClient\Hafas::createOeBB()->trip($data[0]->id);
print_r($data);
