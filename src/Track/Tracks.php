<?php

namespace Aslamhus\SpotifyClient\Track;

use Aslamhus\SpotifyClient\Interfaces\TracksInterface;

/**
 * Tracks
 *
 * Tracks is a collection of Track objects.
 * It provides methods for finding tracks by id, name, etc.
 * Because it does not load the tracks from the Spotify API, it is not an ORM
 * and does not need to adhere to the EntityInterface.
 *
 */
class Tracks implements TracksInterface, \JsonSerializable, \Countable, \IteratorAggregate
{
    private array $tracks = [];

    /**
     * Constructor
     *
     * @param Array<Track> $tracks - the tracks to add to the playlist
     */
    public function __construct(array $tracks)
    {
        $this->tracks = $tracks;

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
        $this->tracks = array_splice($this->tracks, $insertBefore, 0, $tracks);
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

    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->tracks);
    }

    public function jsonSerialize(): array
    {
        return [
            'tracks' => $this->toArray()
            // 'tracks' => array_map(function ($t) { return $t->jsonSerialize(); }, $this->tracks)
        ];
    }

}
