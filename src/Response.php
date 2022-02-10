<?php

namespace Gawsoft\RestApiClientFramework;

class Response {

    private \GuzzleHttp\Psr7\Response $response;
    private $body;

    function __construct(\GuzzleHttp\Psr7\Response $response){
        $this->response = $response;
    }

    function contentEncodings(): array{
        return $this->response->getHeader('x-encoded-content-encoding');
    }

    function contentEncoding(): string {
        $encodings = $this->contentEncodings();
        if(count($encodings) > 0)
            return $encodings[0];
        return '';
    }

    function json(){
        return json_decode($this->body(), false, 512, JSON_BIGINT_AS_STRING);
    }

    function body(){
        if($this->body)
            return $this->body;

        return $this->body = $this->response->getBody()->getContents();
    }

    function contentType(): string{
        return $this->response->getHeader('content-type')[0];
    }

    function contentTypes(): array{
        return $this->response->getHeader('content-type');
    }

    function statusCode(): int{
        return (int)$this->response->getStatusCode();
    }

    function getHeaders(): array{
        return $this->response->getHeaders();
    }

    function save($path): bool{
        return file_put_contents($path, $this->response->getBody());
    }
}