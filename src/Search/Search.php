<?php

namespace Aslamhus\SpotifyClient\Search;

use Aslamhus\SpotifyClient\Interfaces\EntityInterface;
use Aslamhus\SpotifyClient\Spotify;

class Search
{
    private Spotify $spotify;
    private array $query;
    // an array of search results separated by type
    private array $searchResults = [];
    public const TYPES = [
        'album',
        'artist',
        'track',
        // support is coming soon for these types:
        // 'playlist',
        // 'show',
        // 'episode',
        // 'audiobook'
    ];

    public function __construct(Spotify $spotify)
    {
        $this->spotify = $spotify;

    }
    /**
     * Search Spotify API
     *
     * ### Example
     * ```php
     * $search->exec('Steely Dan', 'artist');
     * ```
     *
     * @param string $query - search query, i.e. 'Steely'
     * @param string $types - list of search types, i.e. 'artist','album','track'
     * Allowed values: "album", "artist", "playlist", "track", "show", "episode", "audiobook"
     * @param integer $limit - max number of results, default 5
     * @param integer $offset - offset from beginning of results
     * @param string $market - market code, i.e. 'US'
     *
     * @return ?array
     */
    public function exec(string $query, string $types, int $limit = 30, int $offset = 0, $market = ''): ?array
    {
        // validate the types
        $this->validateTypes($types);
        // reset the search items
        $this->searchResults = [];
        // set the url and query
        $url = 'search';
        $this->query = [
            'q'         => $query,
            'type'      => $types,
            'limit'     => $limit,
            'offset'    => $offset,
        ];
        // add market if provided
        if(!empty($market)) {
            $query['market'] = $market;
        }
        // fetch the data
        $searchResults = $this->spotify->get($url, $this->query);
        // set the search items
        $this->setData($searchResults);
        // return the search items
        return $searchResults;

    }

    /**
     * Set data
     *
     * @param array $searchResult - the search result which contains paginated data for each type of search listed
     * i.e. ['artists' => [], 'albums' => [], 'tracks' => []]
     * @return void
     */
    public function setData(array $searchResults): void
    {
        foreach($searchResults as $type => $paginatedData) {
            // set the search results
            $this->searchResults[$type] = new SearchResult($this->spotify, $type, $paginatedData);
        }

    }



    public function getQuery(): array
    {
        return $this->query;
    }



    /**
     * Get all search results
     *
     * @return array - all search results ['tracks' => SearchResult, 'artists' => SearchResult, 'albums' => SearchResult]
     */
    public function getAllResults(): array
    {
        return $this->searchResults;
    }

    public function getResultsForType(string $type): ?SearchResult
    {
        $this->validateTypes($type);
        // append 's' to the type to match spotify api
        if(substr($type, -1) !== 's') {
            $type .= 's';
        }
        return $this->searchResults[$type] ?? null;
    }

    private function validateTypes(string $types)
    {
        $types = explode(',', $types);
        foreach($types as $type) {
            if(!in_array($type, self::TYPES)) {
                throw new \Exception("Invalid search type: '{$type}'. Allowed values: " . implode(', ', self::TYPES));
            }
        }
    }
}
