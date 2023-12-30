<?php

namespace Aslamhus\SpotifyClient\Track;

use Aslamhus\SpotifyClient\Interfaces\EntityControllerInterface;
use Aslamhus\SpotifyClient\Spotify;
use Aslamhus\SpotifyClient\Track\Track;

/**
 * TrackController for Track ORM
 *
 * TODO: configure this class to fetch track data from Spotify API
 *
 */
class TrackController implements EntityControllerInterface
{
    private Spotify $spotify;

    public function __construct(Spotify $spotify)
    {
        $this->spotify = $spotify;
    }

    public function fetchData(string $trackId = ''): array
    {
        return  $this->spotify->get('https://api.spotify.com/v1/tracks/' . $trackId);
    }




}
