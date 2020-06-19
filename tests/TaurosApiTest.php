<?php

namespace Tauros\Tests;

use Tauros\TaurosApi;
use Tauros\Response;
use PHPUnit\Framework\TestCase;
use \Mockery;

class TaurosApiTest extends TestCase
{
    protected $taurosApi;
    protected $apiKey = "f145b0bcdb6303ebc96ebd164e2387a2968fbbdc";
    protected $apiSecret = "64deee592e79a67b722cc746ce0ea9ab9bf639b55219b987875864d313103sv4";

    public function setUp() : void
    {
        $this->taurosApi = new TaurosApi(
            $apiKey=$this->apiKey,
            $apiSecret=$this->apiSecret,
            $staging=true
        );
    }

    public function testMethodGet() : void
    {
        $data = json_decode(json_encode([
            "email" => "jysa65.dev@gmail.com",
            "first_name" => "Junior",
            "last_name" => "Sanchez",
            "is_active" => true,
        ]));
        $response = new Response;
        $response->statusCode = 200;
        $response->body = $data;

        $mockTaurosApi = Mockery::mock('Tauros\TaurosApi', [$this->apiKey, $this->apiSecret, true])->makePartial();
        $mockTaurosApi->shouldReceive('request')->andReturn($response);

        $path = "/api/v1/profile/";
        $response = $mockTaurosApi->get($path);
        $this->assertSame($response->statusCode, 200);
        $this->assertSame($response->body, $data);
    }

    public function testMethodPost() : void
    {
        $dataResponse = json_decode(json_encode([
            "success" => true,
            "msg" => "0.01 MXN sent to jysa65.dev@gmail.com.",
            "payload" => [
                "amount_sent" => 0.01,
                "coin" => "MXN",
                "fee_amount" => 0,
            ]
        ]));

        $response = new Response;
        $response->statusCode = 200;
        $response->body = $dataResponse;

        $mockTaurosApi = Mockery::mock('Tauros\TaurosApi[request]', [$this->apiKey, $this->apiSecret, true])->makePartial();
        $mockTaurosApi->shouldReceive('request')->andReturn($response);

        $path = "/api/v3/wallets/inner-transfer/";
        $data = array(
            "coin" => "MXN",
            "amount" => 0.01,
            "nip" => "9731",
            "recipient" => "jysa65.dev@gmail.com"
        );

        $response = $mockTaurosApi->post($path, $data);
        $this->assertSame($response->statusCode, 200);
        $this->assertTrue($response->body->success, 200);
        $this->assertEquals($response->body, $dataResponse);
    }

    public function testMethodPatch() : void
    {
        $dataResponse = json_decode(json_encode([
            "success" => true,
            "msg" => "0.01 MXN sent to r.a.s.g610@gmail.com.",
            "payload" => [
                "coin" => "MXN",
                "coin_symbol" => "$",
                "cashback_coin" => null,
                "notify_withdrawal" => false,
                "notify_deposit" => false,
                "notify_user_referred" => true,
                "notify_order_filled" => false,
                "notify_trade" => false,
                "notify_login" => true,
            ]
        ]));

        $response = new Response;
        $response->statusCode = 200;
        $response->body = $dataResponse;

        $mockTaurosApi = Mockery::mock('Tauros\TaurosApi[request]', [$this->apiKey, $this->apiSecret, true])->makePartial();
        $mockTaurosApi->shouldReceive('request')->andReturn($response);

        $path = "/api/v2/preference/";
        $data = array(
            "coin" => "MXN"
        );
        $response = $mockTaurosApi->patch($path, $data);
        $this->assertSame($response->statusCode, 200);
        $this->assertTrue($response->body->success, 200);
        $this->assertEquals($response->body, $dataResponse);
    }

    public function testMethodPut() : void
    {
        $dataResponse = json_decode(json_encode([
            "success" => true,
            "payload" => [
                "first_name" => "Junior",
                "last_name" => "Sanchez",
                "email" => "jysa65.dev@gmail.com",
            ]
        ]));

        $response = new Response;
        $response->statusCode = 200;
        $response->body = $dataResponse;

        $mockTaurosApi = Mockery::mock('Tauros\TaurosApi[request]', [$this->apiKey, $this->apiSecret, true])->makePartial();
        $mockTaurosApi->shouldReceive('request')->andReturn($response);

        $path = "/api/v1/profile/";
        $data = array(
            "first_name" => "Junior"
        );
        $response = $mockTaurosApi->put($path, $data);
        $this->assertSame($response->statusCode, 200);
        $this->assertTrue($response->body->success, 200);
        $this->assertEquals($response->body, $dataResponse);
    }

    public function testMethodDelete() : void
    {

        $response = new Response;
        $response->statusCode = 204;

        $mockTaurosApi = Mockery::mock('Tauros\TaurosApi[request]', [$this->apiKey, $this->apiSecret, true])->makePartial();
        $mockTaurosApi->shouldReceive('request')->andReturn($response);

        $path = "/api/v1/profile/";
        $response = $mockTaurosApi->delete($path);
        $this->assertSame($response->statusCode, 204);
    }

    public function testInvalidAPISecret() : void
    {
        $path = "/api/v1/profile/";
        $response = $this->taurosApi->get($path);
        $this->assertSame($response->statusCode, 401);
    }

    public function testInvalidAPISing() : void
    {
        $mockTaurosApi = Mockery::mock('Tauros\TaurosApi[nonce]', [$this->apiKey, $this->apiSecret, true])->makePartial();
        $mockTaurosApi->shouldReceive('nonce')->andReturn(123456);
        $path = "/api/v1/profile/";
        $response = $mockTaurosApi->request($path, $method='POST');
        $this->assertSame($response->statusCode, 401);
    }

    public function testSignMethod() : void
    {
        $signature = "yyH8Y/AytIjh9OfQ7xzK1ujM59eVe8nMXHAFFCrKrWkbNbEQcIkDTJFf7nfzgQV7LYeitbA0wkHqRRHOg+CVJQ==";
        $mockTaurosApi = Mockery::mock('Tauros\TaurosApi[nonce]', [$this->apiKey, $this->apiSecret, true])->makePartial();
        $mockTaurosApi->shouldReceive('nonce')->andReturn(123456);

        $nonce = $mockTaurosApi->nonce();
        $method = 'POST';
        $path = '/api/v2/test/';
        $data = [
            'age' => 23,
            'email' => 'jysa65.dev@gmail.com',
            'name' => 'Junior Sanchez'
        ];
        $singSignature = $mockTaurosApi->sing($data, $nonce, $method, $path);
        $this->assertEquals($signature, $singSignature);
    }

    public function tearDown() : void
    {
        Mockery::close();
    }
}
