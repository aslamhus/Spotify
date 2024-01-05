<?php

namespace Aslamhus\SpotifyClient\Track;

use Aslamhus\SpotifyClient\Interfaces\TracksInterface;
use Aslamhus\SpotifyClient\Pagination\PaginationController;
use Aslamhus\SpotifyClient\Spotify;
use Aslamhus\SpotifyClient\Track\Track;
use Aslamhus\SpotifyClient\Track\Tracks;

/**
 * Paginated Tracks
 *
 * This class is used to paginate tracks.
 * It is coupled with the Playlist class. It's initial data is fetched from the Playlist class.
 * It can be used to fetch next results and add them to the tracks array.
 *
 * How pagination works:
 *
 * The Spotify API returns a paginated result with a limit of 100 items per page.
 * If a playlist has more than 100 items, it will return a next uri which can be used to fetch the next page.
 * By calling the next() method, the next page will be fetched and the tracks will be added to the tracks array.
 * Though Spotify provides a previous uri, this is not used in this class since we append all tracks to the tracks array.
 *
 * @see https://developer.spotify.com/documentation/web-api/reference/get-playlist
 */
class PaginatedTracks extends PaginationController implements TracksInterface, \JsonSerializable, \Countable
{
    private array $tracks = [];


    public function __construct(Spotify $spotify, $paginatedData, int $limit = 1, int $offset = 0)
    {
        parent::__construct($spotify, $limit, $offset);
        $this->setData($paginatedData);
        $this->pagesLoaded  = 1;

    }

    public function setData(array $paginatedData)
    {

        // parse the paginated data
        $items =  $this->parsePaginatedData($paginatedData);
        // add the tracks to the tracks array
        foreach($items as $item) {
            $track = $item['track'] ?? $item;
            $id = $track['id'] ?? '';
            $this->addTrack(new Track($this->spotify, $id, $track));
        }

    }

    /**
      * Fetch next
      *
      * Fetches the next page of results and appends the tracks to the tracks array
      *
      * @return PaginatedTracks
      */
    public function next(): self
    {

        if($this->hasNext()) {
            // fetch the next page
            $response = $this->fetchNext();
            // set the data
            $this->setData($response);
            // return the tracks
            return $this;
        }
        return null;

    }

    /**
     * Add a track
     *
     * @param Track $track
     * @param integer $position - the position in the playlist to add the track
     * if omitted, the track will be appended to the playlist
     * @return void
     */
    public function addTrack(Track $track, ?int $position = null): void
    {
        if($position === null) {
            $this->tracks[] = $track;
            return;
        }
        // insert at position
        array_splice($this->tracks, $position, 0, [$track]);
        return;
    }

    /**
     * Replace Track
     *
     * @param Track $track
     * @param integer $position
     * @return void
     */
    public function replaceTrack(Track $track, int $position): void
    {

        $this->tracks[$position] = $track;
    }


    /**
     * Replace Track
     *
     * @param Track $track
     * @param integer $position
     * @return void
     */
    public function replaceTracks(Tracks $tracks, int $position): void
    {

        $tracks = $tracks->toArray();
        array_splice($this->tracks, $position, count($tracks), $tracks);
        return;
    }

    public function reorderTracks(int $rangeStart, int $rangeLength, int $insertBefore): void
    {
        // TO DO:
        if($rangeStart + $rangeLength > count($this->tracks)) {
            throw new \Exception('rangeStart and rangeLength out of bounds');
        }
        // 1. remove tracks from array
        $tracks = array_splice($this->tracks, $rangeStart, $rangeLength);
        // 2. insert into new position
        array_splice($this->tracks, $insertBefore, 0, $tracks);
    }

    public function removeTrack(Track $track): void
    {
        $id = $track->getId();
        foreach($this->tracks as $key => $item) {
            if($item->getId() === $id) {
                unset($this->tracks[$key]);
                // reindex the array sequentially
                $this->tracks = array_values($this->tracks);
                return;
            }
        }
    }

    public function removeTracks(Tracks $tracks): void
    {
        $tracks = $tracks->toArray();
        foreach($tracks as $track) {
            $this->removeTrack($track);
        }
    }


    public function count(): int
    {
        return count($this->tracks);
    }

    /**
     * Get the tracks
     *
     * @return array
     */
    public function toArray(): array
    {
        return $this->tracks;
    }

    public function toTracks(): Tracks
    {
        return new Tracks($this->tracks);
    }




    public function findTracksByName(string $name): ?Tracks
    {
        $tracks = new Tracks([]);
        foreach($this->tracks as $track) {
            if($track->getName() === $name) {
                $tracks->addTrack($track);
            }
        }
        if(count($tracks) > 0) {
            return $tracks;
        }
        return null;
    }

    public function findTrackById(string $id): ?Track
    {
        foreach($this->tracks as $track) {
            if($track->getId() === $id) {
                return $track;
            }
        }
        return null;
    }

    public function findTrackByUri(string $uri): ?Track
    {
        foreach($this->tracks as $track) {
            if($track->getUri() === $uri) {
                return $track;
            }
        }
        return null;
    }

    public function findTracksByArtist(string $artist): ?Tracks
    {
        $tracks = new Tracks([]);
        foreach($this->tracks as $track) {
            if($track->getArtist() === $artist) {
                $tracks->addTrack($track);
            }
        }
        if(count($tracks) > 0) {
            return $tracks;
        }
        return null;
    }

    public function getTrackNames(): array
    {
        return $this->getTracksByProperty('name');
    }

    public function getTrackIds(): array
    {
        return $this->getTracksByProperty('id');
    }

    private function getTracksByProperty(string $prop)
    {
        $tracks = [];
        foreach($this->tracks as $track) {
            $tracks[] = $track->$prop;
        }
        return $tracks;
    }

    public function jsonSerialize(): array
    {
        return [
            'tracks' => $this->toArray(),
        ];
    }
}
