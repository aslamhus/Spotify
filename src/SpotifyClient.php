<?php

namespace Aslamhus\SpotifyClient;

use GuzzleHttp\Client;
use Aslamhus\SpotifyClient\Exception\AuthorizationException;
use Psr\Http\Message\ResponseInterface;

class SpotifyClient
{
    protected string $clientId;
    protected string $clientSecret;
    protected Client $client;

    public function __construct(string $clientId, string $clientSecret)
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;

        // set the guzzle client to use for requests
        $this->client = new Client([
            'base_uri' => 'https://api.spotify.com/v1/',
            'timeout'  => 2.0,
        ]);

    }

    public function request(string $method, $uri = '', array $options = []): ResponseInterface
    {
        return $this->client->request($method, $uri, $options);
    }

    /**
     * Send authorization request
     *
     * This method is responsible for sending the authorization request to the Spotify API
     * Used by both SpotifyAccessToken and SpotifyUserAccessToken
     *
     * @param string $endpoint
     * @param array $options
     * @return array
     */
    public function sendAuthorizationRequest($endpoint = 'https://accounts.spotify.com/api/token', array $options = []): array
    {
        try {
            // add auth to options
            $options = [...$options, "auth" => [$this->clientId, $this->clientSecret]];
            $response = $this->request('POST', $endpoint, $options);
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            throw new AuthorizationException($e->getMessage(), $e->getResponse());

        }
        $body = $response->getBody()->getContents();
        return json_decode($body, true);
    }

    public function getClient(): Client
    {
        return $this->client;
    }
}
