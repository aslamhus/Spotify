<?php

namespace Aslamhus\SpotifyClient;

use GuzzleHttp\Client;

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

    public function getClient(): Client
    {
        return $this->client;
    }
}
