<?php

namespace Gawsoft\RestApiClientFramework\Tests;

use Gawsoft\RestApiClientFramework\Base;
use Gawsoft\RestApiClientFramework\Exceptions\ClientException;
use PHPUnit\Framework\TestCase;

class BaseTest extends TestCase {

    function test_download_method() {
        $save_path = '/tmp/test.jpg';
        $base = new Base(new TestClient());
        $base->download('https://httpbin.org/image/jpeg', $save_path);

        $this->assertFileExists($save_path);
        $hash_file = md5_file($save_path);
        $this->assertEquals('a27095e7727c70909c910cefe16d30de',$hash_file);
    }

    function test_method_status_code() {
        $base = new Base(new TestClient());

        $response = $base->method([
            'method' => 'POST',
            'path' => '/status/202',
            'accept_codes' => [202]
        ]);

        $this->assertEquals(202, $response->statusCode());
    }

    function test_method_parse_json() {
        $base = new Base(new TestClient());
        $response = $base->method([
            'method' => 'GET',
            'path' => '/json'
        ]);

        $this->assertObjectHasProperty('slideshow',$response->json());
    }

    function test_method_auth_bearer() {
        $base = new Base(new TestClient());
        $response = $base->method([
            'method' => 'GET',
            'path' => '/bearer'
        ]);
//var_dump($response->json());
        $this->assertEquals(200, $response->statusCode());
        $this->assertObjectHasProperty('authenticated',$response->json());
        $this->assertTrue($response->json()->authenticated);
        $this->assertEquals('abc', $response->json()->token);
    }

    function test_catch_client_exception_from_guzzle() {
        $client = new TestClient();
        $client->api_key = '';

        $base = new Base($client);

        try {
            $base->method([
                'method' => 'POST',
                'path' => '/status/400'
            ]);
        }catch(ClientException $e){
            $this->assertEquals(true, $e->hasResponse());
            $this->assertEquals(400, $e->getResponse()->statusCode());
        }
    }

    function test_method_auth_bearer_should_return_401() {
        $client = new TestClient();
        $client->api_key = '';

        $base = new Base($client);

        $this->expectException(ClientException::class);
        $base->method([
            'method' => 'GET',
            'path' => '/bearer'
        ]);

    }

    function test_method_add_headers() {
        $base = new Base(new TestClient());
        $base->setHeaders([
            'X-abc'=>"aaa",
            'X-AAAA'=>'bbbb'
        ]);

        $response = $base->method([
            'method' => 'GET',
            'path' => '/headers'
        ]);

        $this->assertObjectHasProperty('X-Abc',$response->json()->headers);
        $this->assertObjectHasProperty('X-Aaaa',$response->json()->headers);
        $this->assertEquals('aaa', $response->json()->headers->{'X-Abc'});
        $this->assertEquals('bbbb', $response->json()->headers->{'X-Aaaa'});
    }


    function test_method_parse_gziped_json() {
        $base = new Base(new TestClient());
        $base->setHeaders([
            'X-abc'=>"aaa",
            'X-AAAA'=>'bbbb',
            'Accept-Encoding' => 'gzip',
        ]);

        $response = $base->method([
            'method' => 'GET',
            'path' => '/gzip'
        ]);

        $this->assertTrue(in_array('gzip', $response->contentEncodings()));
        $this->assertEquals('gzip', $response->contentEncoding());
        $this->assertEquals('application/json', $response->contentType());
        $this->assertObjectHasProperty('gzipped', $response->json());
        $this->assertTrue($response->json()->gzipped);
    }

    function test_method_parse_deflate_json() {
        $base = new Base(new TestClient());
        $base->setHeaders([
            'X-abc'=>"aaa",
            'X-AAAA'=>'bbbb',
            'Accept-Encoding' => 'gzip',
        ]);

        $response = $base->method([
            'method' => 'GET',
            'path' => '/deflate'
        ]);

        $this->assertTrue(in_array('deflate', $response->contentEncodings()));
        $this->assertEquals('deflate', $response->contentEncoding());
        $this->assertEquals('application/json', $response->contentType());
        $this->assertObjectHasProperty('deflated', $response->json());
        $this->assertTrue($response->json()->deflated);
    }

}