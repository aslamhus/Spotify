<?php

namespace Aslamhus\SpotifyClient\Album;

use Aslamhus\SpotifyClient\Spotify;
use Aslamhus\SpotifyClient\Artist\Artist;

/**
 * Album ORM
 *
 * Album data can be passed in on construct or fetched from Spotify API
 */
class Album extends AlbumController implements \JsonSerializable
{
    private Spotify $spotify;
    private string $albumId;
    private int $total_tracks = 0;
    private string $album_type = '';
    private array $images = [];
    private string $name = '';
    private string $release_date = '';
    private string $release_date_precision = '';
    private string $type = '';
    private string $uri = '';
    private array $artists = [];
    private array $tracks = [];

    public function __construct(Spotify $spotify, string $albumId, array $data = [])
    {
        parent::__construct($spotify);
        $this->spotify = $spotify;
        $this->albumId = $albumId;
        if(!empty($data)) {
            $this->parseAlbumData($data);
        }
    }


    public function getAlbumId(): string
    {
        return $this->albumId;
    }

    public function getTotalTracks(): int
    {
        return $this->total_tracks;
    }

    public function getAlbumType(): string
    {
        return $this->album_type;
    }

    public function getImages(): array
    {
        return $this->images;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getReleaseDate(): string
    {
        return $this->release_date;
    }

    public function getReleaseDatePrecision(): string
    {
        return $this->release_date_precision;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    public function getArtists(): array
    {
        return $this->artists;
    }



    public function getTracks()
    {
        if(empty($this->tracks)) {
            $this->tracks = parent::fetchTracks($this->albumId);
        }
        return $this->tracks;


    }

    /**
     * Get data
     *
     * Fetches data from Spotify API if not already set
     *
     * @return self
     */
    public function getData(): self
    {
        $response =  parent::fetchData($this->albumId);
        $this->parseAlbumData($response);

        return $this;
    }

    private function parseAlbumData(array $data)
    {
        $this->album_type = $data['album_type'] ?? '';
        $this->total_tracks = $data['total_tracks'] ?? 0;
        $this->images = $data['images'] ?? [];
        $this->name = $data['name'] ?? '';
        $this->release_date = $data['release_date'] ?? '';
        $this->release_date_precision = $data['release_date_precision'] ?? '';
        $this->type = $data['type'] ?? '';
        $this->uri = $data['uri'] ?? '';
        $this->artists = [];
        if(!empty($data['artists'])) {
            foreach($data['artists'] as $artist) {
                $this->artists[] = new Artist($this->spotify, $artist['id'], $artist);
            }
        }

    }



    public function jsonSerialize(): array
    {
        return [
            'album_type' => $this->album_type,
            'total_tracks' => $this->total_tracks,
            'images' => $this->images,
            'name' => $this->name,
            'release_date' => $this->release_date,
            'release_date_precision' => $this->release_date_precision,
            'type' => $this->type,
            'uri' => $this->uri,
            'artists' => $this->artists,
            'tracks' => $this->tracks,
        ];
    }







}
