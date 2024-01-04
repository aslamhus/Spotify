<?php

namespace Aslamhus\SpotifyClient;

use Aslamhus\SpotifyClient\Auth\AccessToken;
use Aslamhus\SpotifyClient\Exception\SpotifyAccessExpiredException;
use Aslamhus\SpotifyClient\SpotifyClient;
use Aslamhus\SpotifyClient\Exception\SpotifyRequestException;
use Psr\Http\Message\ResponseInterface;

/**
 * Spotify API client v1.1.0
 *
 * This class is responsible for making requests to the Spotify API.
 *
 * by @aslamhus
 *
 * ### Usage
 *
 * Spotify requires an access token to make requests.
 * There are two ways to get an access token:
 * 1. Client Credentials - use this method if you are not requesting access to user data
 * 2. Authorization Code - use this method if you are requesting access to user data
 *
 * Use the AuthorizationCode class to get an access token using the Authorization Code flow.
 * Use the ClientCredentials class to get an access token using the Client Credentials flow.
 *
 * ### Example
 *
 * ```php
 * // get access token using client credentials flow
 * $auth = new ClientCredentials($_ENV['SPOTIFY_CLIENT_ID'], $_ENV['SPOTIFY_CLIENT_SECRET']);
 * $spotify = new Spotify($auth);
 * $artistData = $spotify->get('https://api.spotify.com/v1/artists/4Z8W4fKeB5YxbusRsdQVPb');
 * ```
 *
 * @see README.md for more info
 *
 *
 * ### Request Response Codes
 *
 * Status Code	Description
 * 200	OK - The request has succeeded. The client can read the result of the request in the body and the headers of the response.
 * 201	Created - The request has been fulfilled and resulted in a new resource being created.
 * 202	Accepted - The request has been accepted for processing, but the processing has not been completed.
 * 204	No Content - The request has succeeded but returns no message body.
 * 304	Not Modified. See Conditional requests.
 * 400	Bad Request - The request could not be understood by the server due to malformed syntax. The message body will contain more information; see Response Schema.
 * 401	Unauthorized - The request requires user authentication or, if the request included authorization credentials, authorization has been refused for those credentials.
 * 403	Forbidden - The server understood the request, but is refusing to fulfill it.
 * 404	Not Found - The requested resource could not be found. This error can be due to a temporary or permanent condition.
 * 429	Too Many Requests - Rate limiting has been applied.
 * 500	Internal Server Error. You should never receive this error because our clever coders catch them all ... but if you are unlucky enough to get one, please report it to us through a comment at the bottom of this page.
 * 502	Bad Gateway - The server was acting as a gateway or proxy and received an invalid response from the upstream server.
 * 503	Service Unavailable - The server is currently unable to handle the request due to a temporary condition which will be alleviated after some delay. You can choose to resend the request again.
 */
class Spotify
{
    private AccessToken $accessToken;
    private SpotifyClient $client;


    public function __construct(AccessToken $token, SpotifyClient $client)
    {
        $this->accessToken = $token;
        $this->client = $client;
    }




    /**
     * Convenience method to Get resource from Spotify API
     *
     * @param string $url
     * @param array [$query] - optional query parameters, i.e. ['market' => 'US']
     * @param array [$headers] - optional headers to send with the request
     * @param string [$key] - the key to use for the data, i.e. 'json', 'form_params', etc.
     *
     * @return array|null
     */
    public function get(string $url, array $query = [], ?array $headers = null, string $key = 'query'): ?array
    {
        return $this->performRequest('GET', $url, $key, $query, $headers);
    }


    /**
     * Convenience method to Post resource to Spotify API
     *
     * @param string $url
     * @param mixed $data - the data to post
     * @param array [$headers] - optional headers to send with the request
     *
     * @return array|null
     */
    public function post(string $url, mixed $data, ?array $headers = null, string $key = 'json'): ?array
    {
        return $this->performRequest('POST', $url, $key, $data, $headers);
    }


    /**
     * Convenience method to Put resource to Spotify API
     *
     * @param string $url
     * @param mixed $data - the data to put
     * @param array [$headers] - optional headers to send with the request
     * @param string [$key] - the key to use for the data, i.e. 'json', 'form_params', etc.
     * @return array|null
     */
    public function put(string $url, mixed $data, ?array $headers = null, string $key = 'json'): ?array
    {
        return $this->performRequest('PUT', $url, $key, $data, $headers);
    }


    /**
     * Convenience method to Delete resource from Spotify API
     *
     * @param string $url
     * @param mixed $data
     * @param array [$headers] - optional headers to send with the request
     * @param string [$key] - the key to use for the data, i.e. 'json', 'form_params', etc.
     * @return array|null
     */
    public function delete(string $url, mixed $data, ?array $headers = null, string $key = 'json'): ?array
    {
        return $this->performRequest('DELETE', $url, $key, $data, $headers);
    }

    /**
     * Perform request with data
     *
     * Prepares the request with the data and performs the request
     *
     * @param string $method - the request method, i.e. 'POST', 'PUT', etc.
     * @param string $url - the endpoint url
     * @param string $key - the key to use for the data, i.e. 'json', 'form_params', etc.
     * @param mixed $data - the data to post. In the case of 'json', this is an array that will be json encoded.
     * @param ?array $headers - optional headers to send with the request
     *
     * @return ?array
     */
    private function performRequest(string $method, string $url, string $key, $data = null, $headers = null): ?array
    {
        $options = [];
        // conditionally set d post data
        if(!empty($data)) {
            $options[$key] = $data;
            $options['headers'] = $headers ??  ['Content-Type' => 'application/json'];
        }
        return $this->request($method, $url, $options);
    }





    /**
     * Request a resource to Spotify API
     *
     * The request always includes an Authorization header with the value Bearer {access_token}.
     *
     * @param string $type - the request type, i.e. 'GET', 'POST', etc.
     * @param string $url - the endpoint url
     * @param array $options - options for GuzzleHttp\Client
     * @throws SpotifyRequestException
     *
     * @return array|null
     */
    private function request(string $type, string $url, array $options = []): ?array
    {

        // make the request
        echo json_encode($options, JSON_PRETTY_PRINT);
        try {
            $response = $this->client->request($type, $url, $options, $this->accessToken);
        } catch(SpotifyAccessExpiredException $e) {
            $response = $this->handleAccessTokenExpired($type, $url, $options);
        }
        // get the status
        $status = $response->getStatusCode();
        // if status is not 200, throw exception
        if($status !== 200 && $status !== 201 && $status !== 202) {
            throw new SpotifyRequestException('Error getting data, http status code: ' . $status);
        }
        // parse response and decode
        $body = $response->getBody()->getContents();
        return json_decode($body, true);
    }


    /**
     * Handle expired access token
     *
     * @param string $type - the request type, i.e. 'GET', 'POST', etc.
     * @param string $url - the endpoint url
     * @param array $options - options for GuzzleHttp\Client
     * @throws SpotifyRequestException
     *
     * @return array|null
     */
    private function handleAccessTokenExpired(string $type, string $url, array $options = []): ResponseInterface
    {
        // try to refresh token
        $refreshToken =  $this->client->refreshToken($this->accessToken->getRefreshToken());
        if($refreshToken !== null) {
            // if refresh token succeeds, set new access token and try request again
            $this->accessToken = $refreshToken;
            return $this->client->request($type, $url, $options, $this->accessToken);
        }
        throw new SpotifyRequestException('Error refreshing access token');

    }

    /**
     * Search Spotify API
     *
     * ### Example
     * ```php
     * $spotify->search('Steely Dan', 'artist');
     * ```
     *
     * @param string $query - search query, i.e. 'Steely'
     * @param string $types - list of search types, i.e. 'artist','album','track'
     * Allowed values: "album", "artist", "playlist", "track", "show", "episode", "audiobook"
     * @param integer $limit - max number of results, default 5
     * @param integer $offset - offset from beginning of results
     * @param string $market - market code, i.e. 'US'
     * @return array|null
     */
    public function search(string $query, string $types, int $limit = 5, int $offset = 0, string $market = ''): ?array
    {
        $url = '/search';
        $query = [
            'q'         => $query,
            'type'      => $types,
            'limit'     => $limit,
            'offset'    => $offset,
        ];
        if(!empty($market)) {
            $query['market'] = $market;
        }
        return $this->get($url, $query);
    }



}
