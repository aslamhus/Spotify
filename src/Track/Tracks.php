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

    /**
     * Find tracks by name (closest match)
     *
     * @param string $name
     * @return ?array
     */
    public function findTracksByName(string $name): ?array
    {
        $tracks = new Tracks([]);
        foreach($this->tracks as $track) {
            if($track->getName() === $name) {
                $tracks->addTrack($track);
            }
        }
        if($tracks !== null && count($tracks) > 0) {
            return $tracks;
        }
        return null;
    }

    /**
     * Match tracks by name (fuzzy search)
     *
     * @param string $name - the name of the track
     * @return array - array of tracks sorted by relevance
     */
    public function matchTracksByName(string $name): ?array
    {

        return $this->levenshteinSearch($name);
    }

    private function levenshteinSearch(string $name, int $threshold = 3): ?array
    {
        $tracks = [];
        $shortest = -1;

        // loop through words to find the closest
        foreach ($this->tracks as $track) {
            $word = $track->getName();
            // calculate the distance between the input word,
            // and the current word
            $lev = levenshtein($name, $word);
            // check for an exact match
            if ($lev == 0) {

                $tracks[] = ['relevance' => $lev, 'name' => $track->getName(), 'track' => $track];
                break;
            }

            // for all other cases
            if ($lev <= $shortest || $shortest < 0) {
                // log the match and shortest distance
                $shortest = $lev;
                $tracks[] = ['relevance' => $lev, 'name' => $track->getName(),  'track' => $track];
            }
        }


        // sort tracks by relevance
        if(count($tracks) > 0) {
            usort($tracks, function ($a, $b) {
                return $a['relevance'] <=> $b['relevance'];
            });
            return $tracks;
        }

        return $tracks;
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
