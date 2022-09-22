<?php

namespace Gawsoft\RestApiClientFramework\Exceptions;

use Gawsoft\RestApiClientFramework\Response;
use Psr\Http\Message\ResponseInterface;
use \Gawsoft\RestApiClientFramework\Interfaces\ResponseInterface as ClientResponseInterface;

class ClientException extends \Exception{

    private ClientResponseInterface $response;

    public function __construct(
        string $message,
        int $code,
        \Throwable $previous = null,
        ResponseInterface $response = null
    ) {
        parent::__construct($message, $code, $previous);
        $this->response = new Response($response);
    }

    function hasResponse(): bool {
        return $this->response !== null;
    }

    function getResponse(): ClientResponseInterface {
        return $this->response;
    }

}