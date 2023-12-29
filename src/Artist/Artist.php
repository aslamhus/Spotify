<?php

namespace Aslamhus\SpotifyClient\Artist;

use Aslamhus\SpotifyClient\Spotify;
use Aslamhus\SpotifyClient\Album\Album;

/**
 * Artist ORM
 *
 * Gets artist data from Spotify API on construct
 */
class Artist extends ArtistController
{
    private Spotify $spotify;
    private string $artistId;
    private array $data = [];
    private array $tracks = [];
    // array of Album objects
    private array $albums = [];

    public function __construct(Spotify $spotify, string $artistId)
    {
        parent::__construct($spotify);
        $this->spotify = $spotify;
        $this->artistId = $artistId;

    }

    public function getData(): self
    {
        if(empty($this->data)) {
            $this->data = parent::fetchData($this->artistId);
        }
        return $this;
    }

    public function getTracks(string $albumId)
    {
        if(empty($this->tracks)) {
            $this->tracks = parent::fetchTracks($this->artistId, $albumId);
        }
        return $this->tracks;


    }

    /**
     * Gets albums for artist
     *
     *
     * @return array - array of Album objects
     */
    public function getAlbums()
    {
        if(empty($this->albums)) {
            $this->albums = parent::fetchAlbums($this->artistId);
        }
        return $this->albums;
    }





}
