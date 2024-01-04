<?php

namespace Aslamhus\SpotifyClient\Playlist;

use Aslamhus\SpotifyClient\Pagination\PaginationController;
use Aslamhus\SpotifyClient\Interfaces\EntityControllerInterface;
use Aslamhus\SpotifyClient\Interfaces\PaginationInterface;
use Aslamhus\SpotifyClient\Spotify;
use Aslamhus\SpotifyClient\User\User;

class PlaylistsController extends PaginationController implements EntityControllerInterface
{
    public function __construct(Spotify $spotify, int $limit = 10, int $offset = 0)
    {
        parent::__construct($spotify, $limit, $offset);
    }






}
