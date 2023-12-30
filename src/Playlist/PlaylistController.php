<?php

namespace Aslamhus\SpotifyClient\Playlist;

use Aslamhus\SpotifyClient\Spotify;

class PlaylistController
{
    private Spotify $spotify;

    public function __construct(Spotify $spotify)
    {
        $this->spotify = $spotify;
    }

    public function fetchData(string $playlistId): array
    {
        return $this->spotify->get('https://api.spotify.com/v1/playlists/' . $playlistId);
    }
}
