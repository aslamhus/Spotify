<?php

namespace Aslamhus\SpotifyClient;

use Aslamhus\SpotifyClient\Interfaces\AccessTokenInterface;
use GuzzleHttp\Client;

class SpotifyAccessToken extends SpotifyClient implements \JsonSerializable, \IteratorAggregate, AccessTokenInterface
{
    private string $accessToken;
    private string $tokenType;
    private int $expiresIn;
    private string $scope;

    public function __construct(string $clientId, string $clientSecret)
    {
        parent::__construct($clientId, $clientSecret);
        // immediately get access token
        $response = $this->requestAccessToken();
        // populate properties
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
