<?php

namespace Aslamhus\SpotifyClient\Auth;

use Aslamhus\SpotifyClient\Interfaces\AuthorizationInterface;
use Aslamhus\SpotifyClient\Exception\AuthorizationException;
use Aslamhus\SpotifyClient\Auth\AccessToken;
use Aslamhus\SpotifyClient\Spotify;
use Aslamhus\SpotifyClient\SpotifyClient;

/**
 * Authorization Code
 *
 * This class is responsible for getting the user's access token.
 * For Spotify Docs on Auth flow @see https://developer.spotify.com/documentation/web-api/tutorials/code-flow
 *
 * Getting the user's access token has two steps
 *
 */
class AuthorizationCode implements AuthorizationInterface, \JsonSerializable
{
    private AccessToken $accessToken;
    private SpotifyClient $client;

    public function __construct(SpotifyClient $client, string $code, string $redirectUri)
    {
        $this->client = $client;
        // immediately request access token
        $response = $this->requestAccessToken($code, $redirectUri);
        // parse token from response
        $this->accessToken = new AccessToken($response);

    }

    public function getToken(): AccessToken
    {
        return $this->accessToken;
    }

    /**
     *
     * Get authorize url
     *
     * Step 1: Request authorization
     *
     * Perform the request to get the authorization code.
     * This will redirect the user to the callback uri with the code as a query parameter
     * You can now instantiate this class with the code and redirect uri to get the access token
     *
     * @param string $clientId - the client id
     * @param string $redirectUri - the redirect uri to use after authorization
     * @param string $scope - the scope of access needed
     *
     * @return string - the url to redirect the user to
     */
    public static function getAuthorizeUrl(string $clientId, string $redirectUri, string $scope = 'user-read-private user-read-email'): string
    {

        $endpoint = 'https://accounts.spotify.com/authorize';
        $query = [
            'response_type' => 'code',
            'client_id' => $clientId,
            'scope' => $scope,
            'redirect_uri' => $redirectUri,
            'state' => uniqid()
        ];
        $queryString = http_build_query($query);
        return $endpoint . '?' . $queryString;
    }


    /**
     *
     * Request access token
     *
     * Step 2: Request access token - with code from the response of step 1
     *
     * @param string $code - the code received from the callback uri
     * @param string $redirectUri - the redirect uri used in step 1
     * @return array
     * @throws AuthorizationException
     */
    private function requestAccessToken(string $code, string $redirectUri): array
    {
        $options = [
            'form_params' => [
                'code' => $code,
                'redirect_uri' => $redirectUri,
                'grant_type' => 'authorization_code'
            ],
            "headers" => [
                'Content-Type' => 'application/x-www-form-urlencoded'
            ],
        ];
        // client id and secret are added to options in sendAuthorizationRequest
        return $this->client->sendAuthorizationRequest('https://accounts.spotify.com/api/token', $options);
    }

    public function jsonSerialize(): array
    {
        return $this->accessToken->jsonSerialize();
    }





}
