# Spotify API Client (PHP)

## Overview

This PHP package provides a simple and convenient way to interact with the Spotify API. It includes methods for making authenticated requests, retrieving resources, and performing searches. The class utilizes the GuzzleHttp library for HTTP requests.

## Installation

```bash
composer require aslamhus/spotify-client
```

## Usage

### Authorization

Authorization is required to access different scopes of Spotify resources. Choose between `Client Credentials` and `Authorization Code`.

#### Client Credentials

For server-to-server authentication you can use Spotify's `Client Credentials` authorization flow. Simply pass your client id and secret to a `SpotifyAccessToken`, then inject the token into a new `Spotify` instance.

**_Note: User information (like playlists) cannot be accessed with these credentials_**

```php
use Aslamhus\SpotifyClient\Spotify;
use Aslamhus\SpotifyClient\SpotifyClient;
use Aslamhus\SpotifyClient\Auth\ClientCredentials;

// Initialize the Spotify API client with your client id and secret
$client = new SpotifyClient('your-client-id', 'your-client-secret');
// Choose your authorization flow. In this case we use Client Credentials
$credentials = new ClientCredentials($client);
// pass the token into a new Spotify class
$client = new Spotify($credentials->getToken(), $client);
// You're ready to go!
```

#### Authorization Code

The authorization code flow is suitable for long-running applications (e.g. web and mobile apps) where the user grants permission only once.

By requesting authorization from the user, you can gain access to different scopes of Spotify resources.

Please refer to the Spotify docs for more on the `Authorization Code` flow: [https://developer.spotify.com/documentation/web-api/tutorials/code-flow](https://developer.spotify.com/documentation/web-api/tutorials/code-flow)

This authorization has a two step process:

1. Request user authorization.

   Generate an authorization request url with the static method `getAuthorizeUrl`. Once the user is directed to this url, they will be prompted to give permissions to the scopes you specified. Once they grant permission, they will be redirected to the redirect uri you specified.

   Find a list of Spotify API scopes [here](https://developer.spotify.com/documentation/web-api/concepts/scopes).

   **_Note that the redirect uri must be set in your application's [dashboard](https://developer.spotify.com/dashboard)._**

   ```php
   use Aslamhus\SpotifyClient\Auth\AuthorizationCode;
   $url = AuthorizationCode::getAuthorizeUrl('your-client-id', 'http://localhost:8000/callback', 'user-read-private user-read-email');

   ```

   When the user is redirected to the uri you have specified, save the `code` string from the query parameter.

   ```php
   // user lands on the redirect uri after successful login
   $code = $_GET['code'];
   ```

2. Request access token

   You are now ready to request an access token with the user's granted permissions. With the authorization `code` you received, instantiate a `SpotifyUserAccessToken`.

   **_Note: the redirect uri in the 4th argument must match exactly the redirect uri you specified in your application's dashboard and in the previous step._**

   ```php
   $client = new SpotifyClient('your-client-id', 'your-client-secret');
   $credentials = new AuthorizationCode($client, $code, 'http://localhost:8000/callback');
   // instantiate a new Spotify client with user access token
   $spotify = new Spotify($credentials->getToken(), $client);
   // you can now access previously restricted resources
   ```

### Stored Token

If you have saved a token to the database, you can create a new `AccessToken` and pass that to `Spotify`.

```php
use Aslamhus\Spotify\Auth\AccessToken;
$token = new AccessToken([
    'access_token' => '',
    'token_type' => 'Bearer',
    'expires_in' => 3600,
    'scope' => 'user-read-email user-read-private'
]);
$spotify = new Spotify($token, $client);

```

### Get Resource

```php
// Example: Get artist information
$artistData = $client->get('https://api.spotify.com/v1/artists/3WrFJ7ztbogyGnTHbHJFl2');
```

### Search

```php
// Example: Search for artists with a query
$searchResults = $client->search('Steely Dan', 'artist');

// Example: Search for albums with additional parameters
$searchResults = $client->search('Aja', 'album', 5, 0, 'US');
```

### Get all artist albums

```php
use Aslamhus\SpotifyClient\Artist\Artist;
// create Artist object
$artists = new Artist($client, $artistId);
// get all albums for artist
$albums = $artists->getAlbums();
```

### Get all tracks for an album

Carrying on from the last example, we can now load the tracks any album.

```php
$tracks = $albums[0]->getTracks();
```

Note that the data for entity objects like `Album`, `Artist`, and `Track` is lazy loaded. You have to explicitly fetch their data with the relevant `get` method.

```php
use Aslamhus\SpotifyClient\Album\Album;
$album = new Album($client, '4aawyAB9vmqN3uQ7FjRGTy')
// fetch the album data
$album->getData();
// fetch the album tracks
$album->getTracks();
```

### Error Handling

If a non-200 status code is received, an exception is thrown with a descriptive error message.

```php
try {
    $artistData = $spotify->get('https://api.spotify.com/v1/artists/invalid-id');
} catch (\Exception $e) {
    echo 'Error: ' . $e->getMessage();
}
```

## Status Codes

The class includes a list of common Spotify API status codes along with descriptions for reference.

- 200 OK
- 201 Created
- 202 Accepted
- 204 No Content
- 304 Not Modified
- 400 Bad Request
- 401 Unauthorized
- 403 Forbidden
- 404 Not Found
- 429 Too Many Requests
- 500 Internal Server Error
- 502 Bad Gateway
- 503 Service Unavailable

## Contributing

Feel free to contribute by opening issues or submitting pull requests. Your feedback is valuable.

## License

This package is open-source software licensed under the MIT License.
