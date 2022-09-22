<?php

namespace Gawsoft\RestApiClientFramework\Interfaces;

use \Psr\Http\Message\ResponseInterface as PsrResponseInterface;

interface ResponseInterface {
    function contentEncodings(): array;
    function contentEncoding(): string;
    function json();
    function body();
    function contentType(): string;
    function contentTypes(): array;
    function statusCode(): int;
    function save(string $path): bool;
    function psr7Response(): PsrResponseInterface;
}