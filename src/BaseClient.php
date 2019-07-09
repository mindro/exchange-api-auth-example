<?php

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;

class BaseClient
{
    private static $API_BASE_URL = "https://api-exchange.bankera.com";
    private static $AUTH_ENDPOINT = "/oauth/token";
    private static $AUTH_HEADER_PATTER = "Basic base64(%s:%s)";
    private static $DEFAULT_GRANT_TYPE = "client_credentials";

    private $clientId;
    private $clientSecret;

    private $accessToken;
    private $refreshToken;
    private $tokenType;

    public function __construct($clientId, $clientSecret)
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
    }

    public function getAuthorizationHeaderValue()
    {
        if ($this->tokenType == null || $this->refreshToken == null || $this->accessToken == null) {
            $this->authorize();
        }

        return ucfirst($this->tokenType) . " " . $this->accessToken . "." . $this->refreshToken;
    }

    private function authorize()
    {
        $client = new Client();
        $response = $client->request("GET",
            self::$API_BASE_URL.self::$AUTH_ENDPOINT,
            [
                RequestOptions::HEADERS => [
                    'Authorization' => $this->getAuthHeader()
                ],
                RequestOptions::QUERY => [
                    'grant_type' => self::$DEFAULT_GRANT_TYPE
                ]
            ]
        );

        if ($response->getStatusCode() !== 200) {
            throw new Exception("Failed to authorize", $response->getBody());
        }

        $responseBody = json_decode($response->getBody());
        $this->accessToken = $responseBody->access_token;
        $this->refreshToken = $responseBody->refresh_token;
        $this->tokenType = $responseBody->token_type;
    }

    private function getAuthHeader()
    {
        return sprintf(self::$AUTH_HEADER_PATTER, $this->clientId, $this->clientSecret);
    }

}