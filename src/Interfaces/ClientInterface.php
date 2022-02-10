<?php

namespace Gawsoft\RestApiClientFramework\Interfaces;

interface ClientInterface {
    function getTimeout(): int;
    function getApiKey(): string;
    function getEndpoint(): string;
}