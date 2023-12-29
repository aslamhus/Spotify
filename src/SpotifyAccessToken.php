<?php

namespace Aslamhus\SpotifyClient;

use GuzzleHttp\Client;

class SpotifyAccessToken implements \JsonSerializable, \IteratorAggregate
{
    private string $clientId;
    private string $clientSecret;
    private string $accessToken;
    private string $tokenType;
    private int $expiresIn;
    private string $scope;
    private Client $client;

    public function __construct(string $clientId, string $clientSecret)
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->client = new Client([
            'base_uri' => 'https://api.spotify.com/v1/',
            'timeout'  => 2.0,
        ]);
        // immediately get access token
        $response = $this->requestAccessToken();
        $this->accessToken = $response['access_token'];
        $this->tokenType = $response['token_type'];
        $this->expiresIn = $response['expires_in'];
        $this->scope = $response['scope'] ?? '';
    }





    private function requestAccessToken(): array
    {
        $options = [
            "body"      => "grant_type=client_credentials&client_id=" . $this->clientId . "&client_secret=" . $this->clientSecret,
            'headers'   => ['Content-Type' => 'application/x-www-form-urlencoded'],
        ];
        $response = $this->client->request('POST', 'https://accounts.spotify.com/api/token', $options);
        $body = $response->getBody()->getContents();
        return json_decode($body, true);
    }

    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    public function getTokenType(): string
    {
        return $this->tokenType;
    }

    public function getExpiresIn(): int
    {
        return $this->expiresIn;
    }

    public function getScope(): string
    {
        return $this->scope;
    }

    public function getClient(): Client
    {
        return $this->client;
    }


    public function jsonSerialize(): array
    {
        return [
            'access_token'  => $this->accessToken,
            'token_type'    => $this->tokenType,
            'expires_in'    => $this->expiresIn,
            'scope'         => $this->scope,
        ];
    }

    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->jsonSerialize());
    }
}
