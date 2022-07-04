# plentyCSVTagConverter

## How to use

In order to access the REST interface for tag import via plentyMarkets, a customer must first be created in the "
customer" folder. The file in the folder "customer" is called via the GET parameter, which must be specified.

### Convert CSV

First we need to create a Array that contains a unique string as array key and a unique integer.

```php 
$csvHeaderKeys = [
    "length" => 0,
    "width" => 1,
];
```

The purpose of the array key is to store certain data of a column under a unique key. This also means that the integer
value is the column starting with 0 that you want to read from the CSV file. Although this array is not optional and
must be supplied, it has several advantages, which are explained below.

With that you can call the ``class CsvTagConverter``.

```php 
$csvTagConverter = (new CsvTagConverter(CSV_FILENAME, 'csv', ';', true, PID, $csvHeaderKeys))->load();
```

As you can see, the return value of the constructor of CsvTagConverter is the class itself, which allows you to
to create method chaining.

At last you have to use the method ``modifyData``
```php 
$csvTagConverter->modifyData();
```

This method converts your CSV file into an array compatible with json. It is also possible to change the data
data while creating a json object with a prefix. 

```php 
$valuePrefix = [
    "length" => "L",
    "width" => "B"
];

$csvTagConverter->modifyData($valuePrefix);
```

Make sure that the array keys of the ``$valuePrefix`` are the same as the keys in ``$csvHeaderKeys``.

### Define REST-Call meta information

Constants must be specified in the customer file

```php 
const PLENTY_HOST = 'exampleHost';
const CSV_FILENAME = 'exampleCsvFilename';
const ENDPOINT = 'exampleHost/rest';
const APIUSER = 'exampleApiUser';
const APIPASSWORD = 'exampleApiPassword';
const PID = 0000;
```

To start a REST call to plentyMarkets, a token must be generated. Call the function ``callAPILogin``

```php 
$token = callAPILogin('POST', ENDPOINT . '/login', json_encode(['username' => APIUSER, 'password' => APIPASSWORD]));
```

the return value of this function is a token that must be specified for the REST call.

### Call plentyMarkets REST Interface to create Tags

The ``callAPI`` function can be used create POST REST-Call for importing tags.

```php 
callAPI('POST', ENDPOINT . '/tags/bulk', $jsonData, $token);
```

## Example

This example shows a Tag import for length data

```php 
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
```


## Documentation

## WIP