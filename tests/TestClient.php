<?php

namespace Gawsoft\RestApiClientFramework\Tests;
use Gawsoft\RestApiClientFramework\Interfaces\ClientInterface;

class TestClient implements ClientInterface {

    public $api_key='abc';

    function getTimeout(): int
    {
        return 2;
    }

    function getApiKey(): string
    {
        return $this->api_key;
    }

    function getEndpoint(): string
    {
        return 'https://httpbin.org';
    }
}
