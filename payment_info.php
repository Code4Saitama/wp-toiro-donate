<?php
require_once(__DIR__ . '/vendor/autoload.php');

$api_instance = new Swagger\Client\ApiDonatesApi();

try {
    $result = $api_instance->donatesGet();
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling DonatesApi->donatesGet: ', $e->getMessage(), PHP_EOL;
}
?>