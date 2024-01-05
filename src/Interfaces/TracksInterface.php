<?php

namespace Aslamhus\SpotifyClient\Interfaces;

use Aslamhus\SpotifyClient\Track\Tracks;
use Aslamhus\SpotifyClient\Track\Track;

interface TracksInterface
{
    public function findTracksByName(string $name): mixed;

    public function findTrackById(string $id): ?Track;

    public function findTrackByUri(string $uri): ?Track;

    public function findTracksByArtist(string $artist): ?Tracks;

    public function toArray(): array;

    public function addTrack(Track $track, ?int $position): void;

    public function replaceTrack(Track $track, int $position): void;

    public function replaceTracks(Tracks $tracks, int $position): void;

    public function reorderTracks(int $rangeStart, int $rangeLength, int $insertBefore): void;

}
