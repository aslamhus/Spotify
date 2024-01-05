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
        return $this->spotify->get("artists/$artistId");
    }

    // protected function fetchTracks(string $artistId, string $albumId): ?array
    // {
    //     return $this->spotify->get("albums/$artistId/tracks");
    // }

    /**
     * Fetch albums
     *
     * @param string $artistId
     * @return ?array
     */
    protected function fetchAlbums(string $artistId): ?array
    {
        return $this->spotify->get("artists/$artistId/albums");

    }




}
