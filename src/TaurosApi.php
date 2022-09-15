<?php

declare(strict_types=1);

namespace Tauros;

use Tauros\Response;
use Tauros\Exception\ConnectionError;
use Tauros\Exception\ValidationError;

# declare constant
define("API_URL", 'https://api.tauros.io');
define("API_STAGING_URL", 'https://api.staging.tauros.io');


class TaurosApi
{
    protected $apiKey;
    protected $apiSecret;
    protected $staging;

    public function __construct($apiKey, $apiSecret, $staging=false)
    {
        $this->apiKey = $apiKey;
        $this->apiSecret = $apiSecret;
        $this->apiUrl = $staging ? API_STAGING_URL : API_URL;
    }

    public function nonce()
    {
        $microTime = explode(' ', microtime());
        $nonce = $microTime[1] . str_pad(substr($microTime[0], 2, 6), 6, '0');
        return $nonce;
    }

    public function sing($data, $nonce, $method, $path)
    {
        try {
            $data = json_encode($data, JSON_FORCE_OBJECT);
            $message = $nonce . strtoupper($method) . $path . $data;
            $apiSha256 = hash('sha256', utf8_encode($message), true);
            $apiHmac = hash_hmac('sha512', $apiSha256, base64_decode($this->apiSecret), true);
            $signature = base64_encode($apiHmac);
            return $signature;
        } catch (Exception $th) {
            throw new ValidationError($th->getMessage());
        }
    }

    public function request($path, $method='POST', $data=array(), $extras=array())
    {
		$path = $path;
		// var_dump($path);
        $nonce = strval($this->nonce());
        $signature = $this->sing($data=array(), $nonce, $method, $path);

        $headers = array(
            "Content-Type: application/json",
            "Taur-Signature: " . $signature,
            "Taur-Nonce: " . $nonce,
            "Authorization: Bearer " . $this->apiKey
        );
        $headers = array_merge($headers, $extras);

        $options = array(
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_POSTFIELDS =>  json_encode($data, JSON_FORCE_OBJECT),
            CURLOPT_CUSTOMREQUEST => strtoupper($method),
        );

        $handler = curl_init($this->apiUrl . $path);
        curl_setopt_array($handler, $options);
        $result = curl_exec($handler);
        if ($result === false) {
            throw new ConnectionError(curl_error($handler), 1);
        }

        $response = new Response;
        $response->statusCode = curl_getinfo($handler, CURLINFO_HTTP_CODE);
        $response->body = json_decode($result);
        curl_close($handler);
        return $response;
    }

    public function get($path, $params=array())
    {
        return $this->request($path, $method="GET", $data=$params);
    }

    public function post($path, $data=array())
    {
        return $this->request($path, $method="POST", $data);
    }

    public function put($path, $data=array())
    {
        return $this->request($path, $method="PUT", $data);
    }

    public function patch($path, $data=array())
    {
        return $this->request($path, $method="PATCH", $data);
    }

    public function delete($path, $data=array())
    {
        return $this->request($path, $method="DELETE");
    }
}
