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

    function test_method_parse_json() {
        $base = new Base(new TestClient());
        $response = $base->method([
            'method' => 'GET',
            'path' => '/json'
        ]);

        $this->assertObjectHasAttribute('slideshow',$response->json());
    }

    function test_method_auth_bearer() {
        $base = new Base(new TestClient());
        $response = $base->method([
            'method' => 'GET',
            'path' => '/bearer'
        ]);

        $this->assertEquals(200, $response->statusCode());
        $this->assertObjectHasAttribute('authenticated',$response->json());
        $this->assertTrue($response->json()->authenticated);
        $this->assertEquals('abc', $response->json()->token);
    }

    function test_method_auth_bearer_should_return_401() {
        $client = new TestClient();
        $client->api_key = '';

        $base = new Base($client);

        $this->expectException(ClientException::class);
        $response = $base->method([
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

        $this->assertObjectHasAttribute('X-Abc',$response->json()->headers);
        $this->assertObjectHasAttribute('X-Aaaa',$response->json()->headers);
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

        $this->assertObjectHasAttribute('gzipped',$response->json());
        $this->assertTrue($response->json()->gzipped);
    }

}