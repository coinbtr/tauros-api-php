# Tauros api php module

## How To Install
```sh
$ composer require tauros/tauros-api
```

## How To Use
```php
<?php
require __DIR__ . '/vendor/autoload.php';

$apiKey = "You api key";
$apiSecret = "you api secret";

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
?>
```
