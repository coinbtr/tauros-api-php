# Tauros api php module

## How To Install
```sh
$ composer require tauros/tauros-api
```

## How To Use
```php
<?php
require __DIR__ . '/vendor/autoload.php';

$apiKey = "f145b0bcdb6303ebc96ebd164e2387a2968fbbdc";
$apiSecret = "64deee592e79a67b722cc746ce0ea9ab9bf639b55219b987875864d313103sv4";

$taurosApi = new \Tauros\TaurosApi(
    $apiKey=$apiKey,
    $apiSecret=$apiSecret,
    $staging=true // default staging=False
);

$path = "/api/v3/wallets/inner-transfer/";
$data = array(
    "coin" => "MXN",
    "amount" => 0.01,
    "nip" => "9731",
    "recipient" => "jysa65.dev@gmail.com"
);

$response = $taurosApi->post($path, $data);

var_dump($response);
```
