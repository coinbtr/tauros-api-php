<?php

namespace Tauros\Tests;

use Tauros\TaurosApi;
use PHPUnit\Framework\TestCase;

class TaurosApiTest extends TestCase
{
    protected $taurosApi;
    protected $apiKey = "fe4fd5f1e056e4c7aa71bf73f626e855078ec2b8";
    protected $apiSecret = "b0b1cb82db2782375446ce4f337a7ed20f8093f8705712b7f20a6ae94c5c1869";

    public function setUp() : void
    {
        $this->taurosApi = new TaurosApi(
            $apiKey=$this->apiKey,
            $apiSecret=$this->apiSecret,
            $staging=true
        );
    }

    public function testMethodGet()
    {
        $path = "/api/v1/profile/";
        $response = $this->taurosApi->get($path);
        $this->assertSame($response->statusCode, 200);
    }

    public function testMethodPost()
    {
        $path = "/api/v1/enable-developer-mode/";
        $data = array(
            "password" => "hola1425"
        );
        $response = $this->taurosApi->post($path, $data);
        $this->assertSame($response->statusCode, 200);
        $this->assertTrue($response->body->success, 200);
        $this->assertEquals($response->body->msg, "Developer mode activate");
    }
}