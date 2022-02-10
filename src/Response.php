<?php

namespace Gawsoft\RestApiClientFramework;

class Response {

    private \GuzzleHttp\Psr7\Response $response;
    private $body;
    private $headers;

    function __construct(\GuzzleHttp\Psr7\Response $response){
        $this->response = $response;
        $this->headers = $response->getHeaders();
        $this->createBody();
    }

    protected function createBody(){
        if($this->contentEncoding() == 'deflate')
            return $this->body = gzinflate($this->response->getBody()->getContents());
        elseif($this->contentEncoding() == 'gzip')
            return $this->body = gzdecode($this->response->getBody()->getContents());

        if (in_array($this->contentType(), ['application/json','text/plain']))
            $this->body = $this->response->getBody()->getContents();
        else
            $this->body = $this->response->getBody();
    }

    function contentEncodings(): array{
        return $this->response->getHeader('Content-Encoding');
    }

    function contentEncoding(): string {
        $encodings = $this->contentEncodings();
        if(count($encodings) > 0)
            return $encodings[0];
        return '';
    }

    function json(){
        return json_decode($this->body, false, 512, JSON_BIGINT_AS_STRING);
    }

    function body(){
        return $this->body;
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