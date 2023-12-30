<?php

namespace Aslamhus\SpotifyClient\Interfaces;

/**
 * Entity Interface
 *
 * This interface is used to define the methods that all entities must implement
 * Such as the getData() method which returns the entity object
 *
 * Entity interfaces lazy load their data or can have their data passed in on construct
 *
 * Examples of entities: User, Artist, Album, Track, Playlist
 */
interface EntityInterface
{
    public function getData(): self;


    public function setData(array $data): void;
}
