<?php

namespace Aslamhus\SpotifyClient;

use Aslamhus\SpotifyClient\Auth\AccessToken;
use Aslamhus\SpotifyClient\Exception\SpotifyRequestException;
use Aslamhus\SpotifyClient\Exception\SpotifyAccessExpiredException;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Client;

class SpotifyClient
{
    protected string $clientId;
    protected string $clientSecret;
    protected Client $client;
    public const ENDPOINT = 'https://api.spotify.com/v1/';

    public function __construct(string $clientId, string $clientSecret, $endpoint = self::ENDPOINT)
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        // set the guzzle client to use for requests
        $this->client = new Client([
            'base_uri' => $endpoint,
            'timeout'  => 2.0,
        ]);

    }

    /**
     * Request
     *
     * All Spotify API requests go through this method
     *
     * @param string $method
     * @param string $uri
     * @param array [$options]
     * @param AccessToken $token
     * @return ResponseInterface
     */
    public function request(string $method, $uri, array $options = [], AccessToken $token = null): ResponseInterface
    {
        $request = null;
        // add authorization header if access token is set
        if($token !== null) {
            $options = [...$options, 'headers' => ['Authorization' => 'Bearer ' . $token->getAccessToken()]];
        }
        // make the request
        try {
            $request = $this->client->request($method, $uri, $options);
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            // throw granular exception about the request
            throw $this->handleRequestExceptions($e);
        }

        return $request;

    }


    private function handleRequestExceptions(\GuzzleHttp\Exception\ClientException $e)
    {
        $exception = new SpotifyRequestException($e->getMessage(), $e->getResponse());
        // get status code and error
        $statusCode = $exception->getStatusCode();
        $body = $exception->getBody();
        // handle specific errors
        switch($statusCode) {
            case 400:
                // {
                //     "error" : {
                //       "status" : 400,
                //       "message" : "Error parsing JSON."
                //     }
                //   }
                break;
            case 401:
                if(isset($body['error']) &&  $body['error']['message'] === 'The access token expired') {
                    return new SpotifyAccessExpiredException('The access token expired');
                }
                break;

            case 403:
                if(isset($body['error']) &&  $body['error']['message'] === 'Insufficient client scope') {
                    return new SpotifyRequestException('Insufficient client scope');
                }
                break;

        }
        // throw default spotify request exception
        return $exception;
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
            throw new SpotifyRequestException($e->getMessage(), $e->getResponse());

        }
        $body = $response->getBody()->getContents();
        return json_decode($body, true);
    }

    public function refreshToken(string $refreshToken): ?AccessToken
    {
        $options = [
            'form_params' => [
                'grant_type' => 'refresh_token',
                'refresh_token' => $refreshToken,
            ],
        ];
        $response = $this->sendAuthorizationRequest('https://accounts.spotify.com/api/token', $options);
        // if access token is set, return new access token
        if(isset($response['access_token'])) {
            return new AccessToken($response);
        }
        return null;
    }

    public function getClient(): Client
    {
        return $this->client;
    }
}
