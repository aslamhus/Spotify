<?php

namespace Aslamhus\SpotifyClient\User;

use Aslamhus\SpotifyClient\Spotify;
use Aslamhus\SpotifyClient\Interfaces\EntityControllerInterface;

class UserController implements EntityControllerInterface
{
    private Spotify $spotify;

    public function __construct(Spotify $spotify)
    {
        $this->spotify = $spotify;
    }

    public function fetchData(): array
    {
        return $this->spotify->get('https://api.spotify.com/v1/me');
    }

}
