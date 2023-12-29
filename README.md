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
$spotify = new Spotify($accessToken);
```

### Get Resource

```php
// Example: Get artist information
$artistData = $spotify->get('https://api.spotify.com/v1/artists/3WrFJ7ztbogyGnTHbHJFl2');
```

### Search

```php
// Example: Search for artists with a query
$searchResults = $spotify->search('Steely Dan', 'artist');

// Example: Search for albums with additional parameters
$searchResults = $spotify->search('Aja', 'album', 5, 0, 'US');
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
