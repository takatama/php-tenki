<?php

require 'vendor/autoload.php';

use GuzzleHttp\Client;
use Colors\Color;

function show_usage() {
    echo 'Usage: php tenki.php <city,country>' . PHP_EOL;
    exit;
}

function validate_city($city_and_country) {
    if (empty($city_and_country)) {
        show_usage();
    }
}

function validate_response($code, $city_and_country) {
    if ($code === '404') {
        echo 'Invalid city and/or country: ' . $city_and_country;
        show_usage();
    }
}

function get_forecasts($city_and_country) {
    $url = 'http://api.openweathermap.org/data/2.5/forecast/daily?q=' . $city_and_country;
    $client = new Client();
    $json = $client->get($url)->json();
    validate_response($json['cod'], $city_and_country);
    return $json;
}

function show_city($city, $country, $lat, $lon) {
    echo $city . ', ' . $country . ' (lat:' . $lat . ', lon:' . $lon . ')' . PHP_EOL;
}

function show_forecast($time_gmt, $weather, $temp_max_K, $temp_min_K) {
    $time_jpn = $time_gmt + 9 * 60 * 60;
    $temp_max_C = round($temp_max_K - 273.15, 1); //小数点第一位まで表示
    $temp_min_C = round($temp_min_K - 273.15, 1);
    $c = new Color();
    echo gmdate('Y/m/d', $time_jpn) . ' ' . $c($weather)->bold . ' max:' . $c($temp_max_C)->red . ' min:' . $c($temp_min_C)->blue . PHP_EOL;
}

$city_and_country = $argv[1];
validate_city($city_and_country);
$json = get_forecasts($city_and_country);
show_city($json['city']['name'], $json['city']['country'], $json['city']['coord']['lat'], $json['city']['coord']['lon']);
foreach ($json['list'] as $forecast) {
    show_forecast($forecast['dt'], $forecast['weather'][0]['main'], $forecast['temp']['max'], $forecast['temp']['min']);
}