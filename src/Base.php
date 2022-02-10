<?php

namespace Gawsoft\RestApiClientFramework;

use Gawsoft\RestApiClientFramework\Interfaces\ClientInterface;
use GuzzleHttp\Client;
use Gawsoft\RestApiClientFramework\Exceptions\ClientException;

class Base {

    protected ClientInterface $client;
    protected array $headers=[];

    function __construct(ClientInterface $client){
        $this->client = $client;
    }

    function download($url, $save_path): Response{
        try{
            $http = new Client();

            $resource = \GuzzleHttp\Psr7\Utils::tryFopen($save_path, 'w');
            $res = $http->get($url,['sink'=>$resource]);
            return new Response($res);
        }catch(\Exception $e){
            throw new ClientException($e->getMessage(), $e->getCode());
        }
    }

    function setHeaders(array $headers) {
        $this->headers = $headers;
    }

    function method(array $data, int $timeout = null){
        try {
            $http = new Client([
                'timeout' => $timeout ? $timeout : $this->client->getTimeout(),
                'base_uri' => $this->client->getEndpoint()
            ]);

           $api_key = $this->client->getApiKey();

           $toSend = [
               'decode_content' => true
           ];

           if($api_key)
               $toSend['headers'] = array_merge([
                       'authorization' => 'Bearer '.$api_key
                   ], $this->headers);
           else
               $toSend['headers'] = $this->headers;

           if(!in_array($data['method'],['GET','DELETE']) && isset($data['data']))
              $toSend['json'] = $data['data'];

           $res = $http->request(
               $data['method'],
               $data['path'],
               $toSend
           );

           if(!isset($data['accept_codes']))
               $data['accept_codes'] = [200];

            if(!in_array($res->getStatusCode(), $data['accept_codes'])){
                throw new ClientException("Wrong status code: {$res->getStatusCode()}");
            }

            return new Response($res);
        }catch(\Exception $e){
            throw new ClientException($e->getMessage(), $e->getCode());
        }
    }
}