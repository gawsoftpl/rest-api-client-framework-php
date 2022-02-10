# About
Rest client framework for your api client for PHP. In below example how to use this simple script


Write quick your api client
# Example

```php
<?php

namespace TestClient\Client;

use Gawsoft\RestApiClientFramework\Interfaces\ClientInterface;
use Gawsoft\RestApiClientFramework\Base;
use Gawsoft\RestApiClientFramework\Response;
use Gawsoft\RestApiClientFramework\ProjectUrl;
use Gawsoft\RestApiClientFramework\Project;

class TestClient implements ClientInterface {

    private $api_key;
    private $endpoint;
    private $timeout = 30;

    /**
     * @param string $api_key
     */
    function __construct(string $api_key){
        $this->api_key = $api_key;
        $this->endpoint = getenv('WEBSHOTAPI_ENV') == 'dev' ? 'http://localhost:3000' : 'https://api.webshotapi.com/v1';
    }


    /**
     * Download info about your account
     *
     * @return Response
     * @throws ClientException
     */
    function info(): Response{
        $base = new Base($this);
        return $base->method([
            'path' => '/info',
            'method' => 'GET'
        ]);
    }

    /**
     * Set connection timeout in seconds
     *
     * @param $timeout
     */
    function setTimeout(int $timeout){
        $this->timeout = $timeout;
    }

    function getApiKey(): string{
        return $this->api_key;
    }

    function getTimeout(): int{
        return $this->timeout;
    }

    /**
     * Set api endpoint. This method can use for test or if you want to change version of REST api
     * @param $endpoint
     */
    function setEndpoint(string $endpoint){
        $this->endpoint = $endpoint;
    }

    function getEndpoint(): string{
        return $this->endpoint;
    }

    function projects(): Project{
        return new Project($this);
    }

    function projectsUrl(): ProjectUrl{
        return new ProjectUrl($this);
    }

}
```