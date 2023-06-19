<?php

require_once '../vendor/autoload.php';

$data = \HafasClient\Hafas::createDB()->getArrivals(8000152, \Carbon\Carbon::now(), 1);
print_r($data);
