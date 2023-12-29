# Spotify API Client (PHP)

## Overview

This PHP package provides a simple and convenient way to interact with the Spotify API. It includes methods for making authenticated requests, retrieving resources, and performing searches. The class utilizes the GuzzleHttp library for HTTP requests.

## Installation

```bash
composer require aslamhus/spotify-client
```

## Usage

### Initialization

```php
use Aslamhus\SpotifyClient\Spotify;
use Aslamhus\SpotifyClient\SpotifyAccessToken;

// Initialize the Spotify API client with an access token
$accessToken = new SpotifyAccessToken('your-client-id', 'your-client-secret');
$client = new Spotify($accessToken);
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

If you know the album id, you can create the `Album` object without the `Artist` object.
Note that with all objects like `Album`, `Artist`, and `Track`, you have to get their data or their tracks using the relevant get method.

```php
use Aslamhus\SpotifyClient\Album\Album;
$album = new Album($client, '4aawyAB9vmqN3uQ7FjRGTy')
// get the album data
$album->getData();
// get the album tracks
$album->getTracks();
```

### Error Handling

The class handles HTTP status codes gracefully. If a non-200 status code is received, an exception is thrown with a descriptive error message.

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
