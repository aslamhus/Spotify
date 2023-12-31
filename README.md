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

For server-to-server authentication you can use Spotify's `Client Credentials` authorization flow. Simply pass your client id and secret to a `SpotifyAccessToken`, then inject the token into a new `Spotify` instance. The client id and secret can be found in your Spotify Dashboard. For security, it is recommended to store your client id and secret in environment variables.

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

   You are now ready to request an access token with the user's granted permissions. Create a new `AuthorizationCode` credentials object with the code and redirect uri from the previous step. This will generate an access token with the appropriate permissions.

   **_Note: the redirect uri passed to AuthorizationCode must match exactly the redirect uri you specified in your application's dashboard and in the previous step._**

   ```php
   $client = new SpotifyClient('your-client-id', 'your-client-secret');
   $credentials = new AuthorizationCode($client, $code, 'http://localhost:8000/callback');
   // pass the token and client to the Spotify class
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
    'refresh_token' => ''
]);
$spotify = new Spotify($token, $client);
```

### Token expiration and refresh tokens

Spotify tokens are intentionally set to expire after 1 hour. If you have used the `AuthorizationCode` credentials, your token comes with a refresh token. `Aslamhus\Spotify` will automatically handle refreshing your token when you make a request.

### Get Resource

```php
// Example: Get artist information
$artistData = $spotify->get('artists/3WrFJ7ztbogyGnTHbHJFl2');
```

### Search

Search results are paginated. When you perform a search, you can get a `SearchResult` object for each type you've searched for, i.e. 'artist, track, album' would return a separate `SearchResult` object for each type.

**_Note: Search is currently only available for tracks, albums and artists_**

#### Basic search

```php
// Example: Search for artists with a query
$search = new Search($spotify);
$search-exec('Steely Dan', 'artist, track');
// get an associative array of all the search results, i.e. ['tracks' => SearchResult, 'artists' => SearchResult]
$searchResults = $search->getAllResults();
// get the search result items for tracks
$trackItems = $searchResults['tracks']->getItems();
```

#### Using queries with special characters

There is scant documentation on how to format Spotify API searches, but tests show it is best to `urlencode` your queries. For instance, searching for the track "Home Cookin' by Cory Weeds" will return no results if not urlencoded.

#### Get Search Items

```php
$search->exec('Pink Floyd', 'artist, track');
// get a SearchResult object
$searchResult = $search->getResultsForType('track');
// SearchResult objects are json serializable, so you can print them to json
echo json_encode($searchResult, JSON_PRETTY_PRINT);
// You can also get the search items. In this case, a Tracks object is returned
$tracks = $searchResult->getItems();
// print the track names
print_r($tracks->getTrackNames())
```

#### Search Result Types

Each `SearchResult` object contains an array of search items. Depending on the given type you are searching for, each item in the array will be an entity object like `Aritst` or `Album`. If you are searching for tracks, instead of an array of `Track` objects, you will receieve the `Tracks` object, which functions like an array but with additional methods for managing and ssearching for tracks.

#### Get next results

Let's demonstrate how to get the next results.
Each `SearchResult` object has a `next` method, which gets the next set of results
if available. If there are no next results, `next` returns null.

```php
// set the result set limit to 10
$search->exec('Steely Dan', 'artist', 10);
$artistResult = $search->getResultsForType('artist');
if($artistResult->hasNext()){
    // get the next 10 results
    $artistResult->next();
}
// now the search result will have 10 new items appended
count($artistResult->getItems()); // returns 10
```

### Get all artist albums

```php
use Aslamhus\SpotifyClient\Artist\Artist;
// create Artist object
$artists = new Artist($spotify, $artistId);
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
$album = new Album($spotify, '4aawyAB9vmqN3uQ7FjRGTy')
// fetch the album data
$album->getData();
// fetch the album tracks
$album->getTracks();
```

## Playlist

### Get Playlist

**_IMPORTANT: Working with Playlist or User entities requires the AuthorizationCode credentials_**

This example assumes you know the id of the playlist.

```php
$spotify = new Spotify($token, $client);
$user = new User($spotify);
$playlist = new Playlist($spotify, $user, ['id' => 'my-playlist-id-string']);
$playlist->getData();
// all entities are json serializable
echo json_encode($playlist);
```

### Create a new playlist

The static method `create` is a factory function which returns a new instance of the `Playlist` entity.

```php
$playlist = Playlist::create($spotify, $user, [
    'name' => 'My Playlist', // required
    'description' => 'My description' // default ''
    'public' => true // default is false
])
```

### Add Tracks

In order to add a track to a playlist you must know its track id. If you have previously loaded a Track or Tracks object with an id associated with it, you can simply add those.

#### Adding a single track

```php
$track = new Track($spotify, '5xxumuSMspEXt19CGfeiD2');
$playlist->addTrack([$track]);
```

#### Adding a track to a specific index in the playlist

```php
$track = new Track($spotify, '5xxumuSMspEXt19CGfeiD2');
$playlist->addTrack([$track], 3);
```

#### Adding multiple tracks

```php
$tracks = new Tracks([]);
$ids = ['5xxumuSMspEXt19CGfeiD2','6rqhFgbbKwnb9MLmUQDhG6'];
foreach($ids as $id){
    $tracks->addTrack(new Track($spotify, $id));
}
$playlist->addTracks($tracks);
```

### Remove Tracks

```php
$tracksToDelete = $playlist->tracks->findTracksByName('Lazy Day');
// returns null if no tracks found
if($trackToDelete) {
    $playlist->removeTracks($tracksToDelete);
}
```

### Unfollow a playlist

#### A note about deleting / unfollowing playlists from the [Spotify API Docs](https://developer.spotify.com/documentation/web-api/concepts/playlists):

"We have no endpoint for deleting a playlist in the Web API; the notion of deleting a playlist is not relevant within the Spotify’s playlist system. Even if you are the playlist’s owner and you choose to manually remove it from your own list of playlists, you are simply unfollowing it. Although this behavior may sound strange, it means that other users who are already following the playlist can keep enjoying it. Manually restoring a deleted playlist through the Spotify Accounts Service is the same thing as following one of your own playlists that you have previously unfollowed."

```php
$playlist = new Playlist($this->spotify, $this->user, ['id' => 'playlist-id-string']);
$playlist->unfollow();
```

### Additional playlist methods

```php
// Get playlist details
$details = $playlist->getDetails();

// Get playlist cover image
$coverImage = $playlist->getCoverImage();

// Update playlist details
$updateOptions = [
    'name' => 'Updated Playlist Name',
    'description' => 'Updated playlist description',
    'public' => false,
];
$playlist->changeDetails($updateOptions);

// Update playlist cover image
$filePath = "/path/to/my/file.jpg";
$playlist->updateCoverImage($filePath);

// Reorder playlist tracks
$playlist->reorderTracks(2, 0);

// Replace playlist tracks
$newTracks = new Tracks([new Track('new-track-uri-1'), new Track('new-track-uri-2')]);
$playlist->replaceTracks($newTracks);
```

### Error Handling

If a non-200 status code is received, an exception is thrown with a descriptive error message.

```php
try {
    $artistData = $spotify->get('artists/invalid-id');
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
