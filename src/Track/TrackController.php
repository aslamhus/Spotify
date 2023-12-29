<?php

namespace Aslamhus\SpotifyClient\Track;

use Aslamhus\SpotifyClient\Spotify;
use Aslamhus\SpotifyClient\Track\Track;

/**
 * TrackController for Track ORM
 *
 * TODO: configure this class to fetch track data from Spotify API
 *
 */
class TrackController
{
    private Spotify $spotify;

    public function __construct(Spotify $spotify)
    {
        $this->spotify = $spotify;
    }

    protected function fetchTrackData(string $albumId): ?array
    {
        $response =  $this->spotify->get('https://api.spotify.com/v1/tracks/' . $albumId . '/tracks');
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
