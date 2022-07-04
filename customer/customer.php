<?php

const PLENTY_HOST = 'example.de';
const CSV_FILENAME = 'example';
const ENDPOINT = 'https://example.de/rest';
const APIUSER = 'ApiUser';
const APIPASSWORD = 'ApiPassword';
const PID = 0000;


$token = callAPILogin('POST', ENDPOINT . '/login', json_encode(['username' => APIUSER, 'password' => APIPASSWORD]));

$csvHeaderKeys = [
    "length" => 0,
    "width" => 1,
];

$valuePrefix = [
    "length" => "L",
    "width" => "B"
];

$csvTagConverter = (new CsvTagConverter(
    CSV_FILENAME,
    'csv',
    ';',
    true,
    PID,
    $csvHeaderKeys))->load();
$csvTagConverter->modifyData($valuePrefix);

/**
 * Import Width Tags!
 */

$arraySize = count($csvTagConverter->tagOrderedJsonObject["width"]) - 1;
$pufferSize = 20;
$sleeps = 0;

foreach ($csvTagConverter->tagOrderedJsonObject["width"] as $key => $jsonData) {

    if ($key >= $pufferSize && $key != $arraySize) {
        $pufferSize += 20;
        $sleeps++;

        echo $sleeps . "\n";
        sleep(5);
    }

    callAPI('POST', ENDPOINT . '/tags/bulk', $jsonData, $token);
}

/**
 * Import Length Tags!
 */


$arraySize = count($csvTagConverter->tagOrderedJsonObject["length"]) - 1;
$pufferSize = 20;
$sleeps = 0;

foreach ($csvTagConverter->tagOrderedJsonObject["length"] as $key => $jsonData) {

    if ($key >= $pufferSize && $key != $arraySize) {
        $pufferSize += 15;
        $sleeps++;

        echo $sleeps . "\n";
        sleep(5);
    }

    callAPI('POST', ENDPOINT . '/tags/bulk', $jsonData, $token);
}