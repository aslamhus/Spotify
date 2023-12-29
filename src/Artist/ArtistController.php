<?php

namespace Aslamhus\SpotifyClient\Artist;

use Aslamhus\SpotifyClient\Spotify;
use Aslamhus\SpotifyClient\Album\Album;

/**
 * ArtistController for Artist ORM
 */
class ArtistController
{
    private Spotify $spotify;

    public function __construct(Spotify $spotify)
    {
        $this->spotify = $spotify;
    }

    protected function fetchData(string $artistId): ?array
    {
        return $this->spotify->get('https://api.spotify.com/v1/artists/' . $artistId);
    }

    protected function fetchTracks(string $artistId, string $albumId): ?array
    {
        return $this->spotify->get('https://api.spotify.com/v1/albums/' . $artistId . '/tracks');
    }

    protected function fetchAlbums(string $artistId): ?array
    {
        $response =  $this->spotify->get('https://api.spotify.com/v1/artists/' . $artistId . '/albums');
        // parse response into array of Album objects
        return $this->parseAlbumsResponse($response);

    }

    private function parseAlbumsResponse(array $response): array
    {
        $albums = [];
        foreach($response['items'] as $album) {
            $albums[] = new Album($this->spotify, $album['id'], $album);
        }
        return $albums;
    }


}
