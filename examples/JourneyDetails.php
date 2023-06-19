<?php

require_once '../vendor/autoload.php';

$data = \HafasClient\Hafas::createDB()->searchTrips('ICE 28', new DateTime('today 00:00'), new DateTime('today 23:00'));
$data = \HafasClient\Hafas::createDB()->trip($data[0]->id);
print_r($data);
