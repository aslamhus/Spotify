<?php

namespace Aslamhus\SpotifyClient;

use Aslamhus\SpotifyClient\Interfaces\AccessTokenInterface;
use GuzzleHttp\Client;

/**
 * SpotifyUserAccessToken
 *
 * This class is responsible for getting the user's access token.
 * For Spotify Docs on Auth flow @see https://developer.spotify.com/documentation/web-api/tutorials/code-flow
 *
 * Getting the user's access token has two steps
 *
 */
class SpotifyUserAccessToken extends SpotifyClient implements \JsonSerializable, AccessTokenInterface
{
    private string $accessToken;
    private string $tokenType;
    private int $expiresIn;
    private string $scope;

    public function __construct(string $clientId, string $clientSecret, string $code, string $redirectUri)
    {
        parent::__construct($clientId, $clientSecret);
        // immediately request access token
        $response = $this->requestAccessToken($code, $redirectUri);
        // populate properties
        $this->accessToken = $response['access_token'];
        $this->tokenType = $response['token_type'];
        $this->expiresIn = $response['expires_in'];
        $this->scope = $response['scope'] ?? '';

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
     */
    private function requestAccessToken(string $code, string $redirectUri): array
    {
        $options = [
            'form_params' => [
                'code' => $code,
                'redirect_uri' => $redirectUri,
                'grant_type' => 'authorization_code'
            ],
            "auth" => [$this->clientId, $this->clientSecret],
            "headers" => [
                'Content-Type' => 'application/x-www-form-urlencoded'
            ],
            'debug' => true
        ];
        print_r($options);
        $response = null;
        try {
            $response = $this->client->request('POST', 'https://accounts.spotify.com/api/token', $options);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());

        }
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
}
