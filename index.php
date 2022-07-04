<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

define('PROJECT_PATH', dirname(__FILE__) . DIRECTORY_SEPARATOR);

require_once PROJECT_PATH . 'app/plentyRESTApi.php';
require_once PROJECT_PATH . 'app/AbstractCsvConverter.php';
require_once PROJECT_PATH . 'app/CsvTagConverter.php';

if (!isset($_GET['customer'])) {
    exit("Missing GET Parameter customer");
}

if (!file_exists(PROJECT_PATH . "/customer/{$_GET['customer']}.php")) {
    exit("customer not found");
}

require_once PROJECT_PATH . "customer\\{$_GET['customer']}.php";