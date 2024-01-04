<?php

namespace Aslamhus\SpotifyClient\Track;

use Aslamhus\SpotifyClient\Interfaces\EntityControllerInterface;
use Aslamhus\SpotifyClient\Interfaces\PaginationInterface;
use Aslamhus\SpotifyClient\Spotify;
use Aslamhus\SpotifyClient\User\User;

class TracksController implements EntityControllerInterface, PaginationInterface
{
    private Spotify $spotify;
    private User $user;
    private string $next = '';
    private string $previous = '';
    private int $limit = 10;
    private int $offset = 0;
    private int $total = 0;

    public function __construct(Spotify $spotify, User $user, int $limit = 10, int $offset = 0)
    {
        $this->spotify = $spotify;
        $this->user = $user;
        $this->limit = $limit;
        $this->offset = $offset;
    }

    /**
     * Fetch data
     *
     * Returns a pagination result
     *
     * @param string $playlistId
     * @return array
     */
    public function fetchData(): array
    {
        // get playlists for user
        $paginationResult =  $this->spotify->get("users/{$this->user->getId()}/playlists", [
            'limit' => $this->limit,
            'offset' => $this->offset
        ]);
        // set pagination data
        $this->total = $paginationResult['total'] ?? 0;
        $this->next = $paginationResult['next'] ?? '';
        $this->previous = $paginationResult['previous'] ?? '';
        $this->limit = $paginationResult['limit'] ?? 0;
        $this->offset = $paginationResult['offset'] ?? 0;
        // return items
        return $paginationResult['items'] ?? [];
    }

    public function fetchNext(): array
    {
        if(empty($this->next)) {
            return [];
        }
        return $this->spotify->get($this->next);

    }

    public function fetchPrevious(): array
    {
        if(empty($this->previous)) {
            return [];
        }
        return $this->spotify->get($this->previous);
    }

    public function getTotal(): int
    {
        return $this->total;
    }




}
