<?php

require 'vendor/autoload.php';

use Webnic\WebnicSDK\WebnicSDK;

$WebnicSDK = new WebnicSDK(
    'api-username',
    'api-secret',
    [] // leave it empty if using OTE
);

try {
    $response = $WebnicSDK->getAccountBalance();
    print_r($response);
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
    error_log('Exception: ' . $e->getMessage());
}
