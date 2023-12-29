<?php

namespace Aslamhus\SpotifyClient\Album;

use Aslamhus\SpotifyClient\Spotify;
use Aslamhus\SpotifyClient\Track\Track;

/**
 * AlbumController for Album ORM
 *
 * @see https://developer.spotify.com/documentation/web-api/reference/get-an-album
 */
class AlbumController
{
    private Spotify $spotify;

    public function __construct(Spotify $spotify)
    {
        $this->spotify = $spotify;
    }


    protected function fetchData(string $albumId): ?array
    {
        return $this->spotify->get('https://api.spotify.com/v1/albums/' . $albumId);
    }

    protected function fetchTracks(string $albumId): ?array
    {
        $response =  $this->spotify->get('https://api.spotify.com/v1/albums/' . $albumId . '/tracks');
        // parse response into array of track objects
        return $this->parseTracksResponse($response);
    }

    private function parseTracksResponse(array $response): array
    {
        $tracks = [];
        foreach($response['items'] as $track) {
            $tracks[] = new Track($this->spotify, $track['id'], $track);
        }
        return $tracks;
    }



}
