<?php

namespace RickWest\WordPress;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class Client
{
    private string $baseUrl;

    private ?array $basicAuth;

    public function __construct(string $baseUrl = null)
    {
        $this->baseUrl = $baseUrl ?? strval(config('wordpress-api.url'));
        $this->basicAuth = null;
    }

    /**
     * @param ?string $user_or_token
     * @param ?string $password
     * @return void
     */
    public function auth(?string $user_or_token = null, ?string $password = null): void
    {
        if(null === $user_or_token) {
            $this->basicAuth = null;
        } else if(!isset($password)) {
            $this->basicAuth = explode(':', $user_or_token);
        } else {
            $this->basicAuth = [$user_or_token, $password];
        }
    }

    /**
     * @param string $method
     * @param string $endpoint
     * @param array $options
     * @return Response
     */
    public function send(string $method, string $endpoint, array $options = []): Response
    {

        if(is_array($this->basicAuth) && count($this->basicAuth) === 2) {
            return Http::withBasicAuth($this->basicAuth[0], $this->basicAuth[1])->send($method, $this->baseUrl.$endpoint, $options);
        }

        return Http::send($method, $this->baseUrl.$endpoint, $options);
    }
}
