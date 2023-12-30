<?php

namespace Aslamhus\SpotifyClient\Auth;

use Aslamhus\SpotifyClient\Interfaces\AuthorizationInterface;
use Aslamhus\SpotifyClient\Exception\AuthorizationException;
use Aslamhus\SpotifyClient\Auth\AccessToken;
use Aslamhus\SpotifyClient\SpotifyClient;

class ClientCredentials implements AuthorizationInterface, \JsonSerializable
{
    private AccessToken $accessToken;
    private SpotifyClient $client;


    public function __construct(SpotifyClient $client)
    {
        $this->client = $client;
        // immediately get access token
        $response = $this->requestAccessToken();
        // parse token from response
        $this->accessToken = new AccessToken($response);
    }

    public function getToken(): AccessToken
    {
        return $this->accessToken;
    }



    /**
     * Request access token
     *
     * Requst client credentials access token
     *
     * @return array
     * @throws AuthorizationException
     */
    private function requestAccessToken(): array
    {
        $options = [
            'form_params' => [
                'grant_type' => 'client_credentials'
            ],
            // "body"      => "grant_type=client_credentials&client_id=" . $this->clientId . "&client_secret=" . $this->clientSecret,
            'headers'   => ['Content-Type' => 'application/x-www-form-urlencoded'],
        ];
        return $this->client->sendAuthorizationRequest('https://accounts.spotify.com/api/token', $options);
    }

    public function jsonSerialize(): mixed
    {
        return $this->accessToken->jsonSerialize();
    }


}
