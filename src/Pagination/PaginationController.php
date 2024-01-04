<?php

namespace Aslamhus\SpotifyClient\Pagination;

use Aslamhus\SpotifyClient\Spotify;

class PaginationController
{
    protected Spotify $spotify;
    protected string $next = '';
    protected string $previous = '';
    // Spotify defaults to 100 and currently there is no way to change this
    protected int $limit = 100;
    protected int $offset = 0;
    protected int $total = 0;
    public int $pages = 0;
    public int $pagesLoaded = 0;

    public function __construct(Spotify $spotify)
    {
        $this->spotify = $spotify;
    }

    /**
     * Fetch data
     *
     * Parses a paginated result and returns the items
     *
     * @param string $playlistId
     * @return array
     */
    public function fetchData(string $path): array
    {
        // get playlists for user
        $paginationResult =  $this->spotify->get($path);
        $this->pagesLoaded++;
        return $this->parsePaginatedData($paginationResult);
    }


    /**
     * Fetch next
     *
     * Fetches the next page of results
     *
     * @return ?array - returns null if there is no next page
     */
    public function fetchNext(): array
    {
        if(empty($this->next)) {
            return null;
        }
        $paginatedResult = $this->spotify->get($this->next);
        $this->pagesLoaded++;
        return $paginatedResult;

    }



    /**
     * Parse paginated data
     *
     * Gets the total, limit and offset and returns the items
     *
     * @param array $data
     * @return array
     */
    public function parsePaginatedData(array $data): array
    {
        $this->total = $data['total'] ?? 0;
        $this->limit = $data['limit'] ?? $this->limit;
        $this->offset = $data['offset'] ?? $this->offset;
        $this->next = $data['next'] ?? '';
        $this->previous = $data['previous'] ?? '';
        $this->pages = ceil($this->total / $this->limit);
        return $data['items'] ?? [];

    }



    public function getTotal(): int
    {
        return $this->total;
    }

    public function getPagination(): array
    {
        return [
            'limit' => $this->limit,
            'offset' => $this->offset,
            'total' => $this->total,
            'pages' => $this->pages,
            'pagesLoaded' => $this->pagesLoaded,
            'next' => $this->next,
            'previous' => $this->previous,
        ];
    }




}
