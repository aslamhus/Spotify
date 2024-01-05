<?php

namespace Aslamhus\SpotifyClient\Track;

use Aslamhus\SpotifyClient\Interfaces\EntityInterface;
use Aslamhus\SpotifyClient\Spotify;
use Aslamhus\SpotifyClient\Artist\Artist;

/**
 * Track ORM
 *
 * Tack data can be passed in on construct or fetched from Spotify API
 *
 * @see https://developer.spotify.com/documentation/web-api/reference/get-track
 */
class Track extends TrackController implements EntityInterface, \JsonSerializable
{
    private Spotify $spotify;
    public string $id;
    public array $artists = [];
    public array $available_markets = [];
    public int $disc_number = 0;
    public int $duration_ms = 0;
    public bool $explicit = false;
    public array $external_ids = [];
    public array $external_urls = [];
    public string $href = '';
    public bool $is_local = false;
    public string $name = '';
    public int $popularity = 0;
    public string $preview_url = '';
    public int $track_number = 0;
    public string $type = '';
    public string $uri = '';


    public function __construct(Spotify $spotify, string $id, array $data = [])
    {
        parent::__construct($spotify);
        $this->spotify = $spotify;
        $this->id = $id;
        if(!empty($data)) {
            $this->setData($data);
        } else {
            // set uri manually from track id
            $this->uri = "spotify:track:$id";
        }
    }

    public function getId(): string
    {
        return $this->id;
    }

    /**
     * Get artists
     *
     * @return Array<Artist>
     */
    public function getArtists(): array
    {
        return $this->artists;
    }

    public function getAvailableMarkets(): array
    {
        return $this->available_markets;
    }

    public function getDiscNumber(): int
    {
        return $this->disc_number;
    }

    public function getDurationMs(): int
    {
        return $this->duration_ms;
    }

    public function getExplicit(): bool
    {
        return $this->explicit;
    }

    public function getExternalIds(): array
    {
        return $this->external_ids;
    }

    public function getExternalUrls(): array
    {
        return $this->external_urls;
    }

    public function getHref(): string
    {
        return $this->href;
    }

    public function getIsLocal(): bool
    {
        return $this->is_local;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPopularity(): int
    {
        return $this->popularity;
    }

    public function getPreviewUrl(): string
    {
        return $this->preview_url;
    }

    public function getTrackNumber(): int
    {
        return $this->track_number;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    public function getData(): self
    {
        $response = parent::fetchData($this->id);
        $this->setData($response);
        return $this;
    }


    public function setData(array $data): void
    {

        $this->id = $data['id'];
        $this->artists = [];
        if(isset($data['artists'])) {
            foreach($data['artists'] as $artist) {
                $this->artists[] = new Artist($this->spotify, $artist['id'], $artist);
            }
        }
        $this->available_markets = $data['available_markets'] ?? [];
        $this->disc_number = $data['disc_number'] ?? 0;
        $this->duration_ms = $data['duration_ms'] ?? 0;
        $this->explicit = $data['explicit'] ?? false;
        $this->external_ids = $data['external_ids'] ?? [] ;
        $this->external_urls = $data['external_urls'] ?? [];
        $this->href = $data['href'] ?? '';
        $this->is_local = $data['is_local']  ?? false;
        $this->name = $data['name'] ?? '';
        $this->popularity = $data['popularity'] ?? 0;
        $this->preview_url = $data['preview_url']   ?? '';
        $this->track_number = $data['track_number'] ?? 0;
        $this->type = $data['type'] ?? '';
        $this->uri = $data['uri']   ?? '';


    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'artists' => $this->artists,
            'available_markets' => $this->available_markets,
            'disc_number' => $this->disc_number,
            'duration_ms' => $this->duration_ms,
            'explicit' => $this->explicit,
            'external_ids' => $this->external_ids,
            'external_urls' => $this->external_urls,
            'href' => $this->href,
            'is_local' => $this->is_local,
            'name' => $this->name,
            'popularity' => $this->popularity,
            'preview_url' => $this->preview_url,
            'track_number' => $this->track_number,
            'type' => $this->type,
            'uri' => $this->uri,

        ];
    }
}
