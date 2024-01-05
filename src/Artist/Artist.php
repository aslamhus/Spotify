<?php

namespace Aslamhus\SpotifyClient\Artist;

use Aslamhus\SpotifyClient\Spotify;
use Aslamhus\SpotifyClient\Album\Album;
use Aslamhus\SpotifyClient\Interfaces\EntityInterface;

/**
 * Artist ORM
 *
 * Gets artist data from Spotify API on construct
 */
class Artist extends ArtistController implements EntityInterface, \JsonSerializable
{
    private Spotify $spotify;
    private string $id;
    private array $data = [];
    private array $external_urls = [];
    private array $followers = [];
    private array $genres = [];
    private string $href = '';
    private array $images = [];
    private string $name = '';
    private int $popularity = 0;
    private string $type = '';
    private string $uri = '';
    // array of Album objects
    private array $albums = [];

    public function __construct(Spotify $spotify, string $id, array $data = [])
    {
        parent::__construct($spotify);
        $this->spotify = $spotify;
        $this->id = $id;
        if(!empty($data)) {
            $this->setData($data);
        } else {
            // set the uri manually based on the artist id
            $this->uri = "spotify:artist:{$this->id}";
        }


    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {

        return $this->name;
    }

    public function getData(): self
    {
        if(empty($this->data)) {
            $this->setData(parent::fetchData($this->id));
        }
        return $this;
    }

    public function setData($data): void
    {
        $this->external_urls = $data['external_urls'] ?? [];
        $this->followers = $data['followers'] ?? [];
        $this->genres = $data['genres'] ?? [];
        $this->href = $data['href'] ?? '';
        $this->images = $data['images'] ?? [];
        $this->name = $data['name'] ?? '';
        $this->popularity = $data['popularity'] ?? 0;
        $this->type = $data['type'] ?? '';
        $this->uri = $data['uri'] ?? '';
        if(!empty($data['albums'])) {
            $this->albums = $this->parseAlbumsData($data['albums']);
        }

    }

    /**
     * Set albums
     *
     * @param array $albumsData
     * @return void
     */
    private function setAlbums(array $albumsData): void
    {
        $this->albums = $this->parseAlbumsData($albumsData);
    }



    /**
     * Gets albums for artist
     *
     *
     * @return ?Array<Album> - array of Album objects
     */
    public function getAlbums(): ?array
    {
        if(empty($this->albums)) {
            $albums = parent::fetchAlbums($this->id);
            $this->setAlbums($albums);

        }
        return $this->albums;
    }


    /**
    * Parse albums response
    *
    * @param array $response
    * @return Array<Album>
    */
    private function parseAlbumsData(array $albumsData): array
    {
        $albums = [];
        foreach($albumsData['items'] as $album) {
            $albums[] = new Album($this->spotify, $album['id'], $album);
        }
        return $albums;
    }

    public function jsonSerialize(): array
    {
        return [
            'external_urls' => $this->external_urls,
            'followers' => $this->followers,
            'genres' => $this->genres,
            'href' => $this->href,
            'images' => $this->images,
            'name' => $this->name,
            'popularity' => $this->popularity,
            'type' => $this->type,
            'uri' => $this->uri,
            'albums' => $this->albums
        ];
    }



}
