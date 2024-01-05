<?php

namespace Aslamhus\SpotifyClient\Search;

use ArrayIterator;
use Aslamhus\SpotifyClient\Pagination\PaginationController;
use Aslamhus\SpotifyClient\Spotify;
use Aslamhus\SpotifyClient\Artist\Artist;
use Aslamhus\SpotifyClient\Album\Album;
use Aslamhus\SpotifyClient\Playlist\Playlist;
use Aslamhus\SpotifyClient\Track\Track;
use Aslamhus\SpotifyClient\Track\Tracks;

class SearchResult extends PaginationController implements \IteratorAggregate, \JsonSerializable, \Countable
{
    private string $type;
    private array $searchItems = [];

    public function __construct(Spotify $spotify, $type, $paginatedData, int $limit = 1, int $offset = 0)
    {
        parent::__construct($spotify);
        $this->type = $type;
        // set the pagination data (limit, offset, next, etc in the parent class)
        $items = $this->parsePaginatedData($paginatedData);
        // manually set the pages loaded to 1 because we are setting the first page manually
        $this->pagesLoaded = 1;
        // set the items
        $this->parseSearchItems($items);


    }

    /**
     * Parse search items
     *
     * Parses the search items and creates the appropriate entity object
     *
     * @param array $items
     * @return Array<EntityInterface> - an array of entity objects
     */
    private function parseSearchItems(array $items): ?array
    {
        $entityItems = [];
        // create a new entity object based on the type
        switch($this->type) {
            case 'artists':
                foreach($items as $item) {
                    $entityItems[] = new Artist($this->spotify, $item['id'], $item);
                }
                break;
            case 'albums':
                foreach($items as $item) {
                    $entityItems[] = new Album($this->spotify, $item['id'], $item);
                }
                break;
            case 'tracks':
                // Note: we do not return a Tracks object, we return an array of Track objects
                // Why? Because we want to be able to merge the search results with other search results
                foreach($items as $item) {
                    $entityItems[] = new Track($this->spotify, $item['id'], $item);
                }
                break;

            default:
                throw new \Exception("Unrecognized search type: '{$this->type}'. Currently only 'artist', 'album', and 'track' are supported.");
        }
        // merge the search items
        $this->searchItems = array_merge($this->searchItems, $entityItems);

        return $entityItems;
    }

    /**
     * Get search items
     *
     * @return mixed
     */
    public function getItems(): mixed
    {
        if($this->type === 'tracks') {
            // return Tracks object
            return new Tracks($this->searchItems);
        }
        return $this->searchItems;
    }

    /**
    * Fetch next
    *
    * Fetches the next page of results and appends the tracks to the tracks array
    *
    * @return Array<EntityInterface> - returns next items or null if there is no next page
    */
    public function next(): ?array
    {

        if($this->hasNext()) {
            // fetch the next page
            $response = $this->fetchNext();
            $resultsForType = $response[$this->type] ?? [];
            // parse the paginated data
            $items = parent::parsePaginatedData($resultsForType);
            // append the search items
            $nextItems =  $this->parseSearchItems($items);
            // return the tracks
            return $nextItems;
        }
        return null;

    }

    public function count(): int
    {
        return count($this->searchItems);
    }

    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->searchItems);
    }

    public function jsonSerialize(): mixed
    {
        return $this->searchItems;
    }
}
