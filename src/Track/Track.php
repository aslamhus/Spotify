<?php

namespace Aslamhus\SpotifyClient\Track;

use Aslamhus\SpotifyClient\Interfaces\EntityInterface;
use Aslamhus\SpotifyClient\Spotify;

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
    private string $trackId;
    private array $artists = [];
    private array $available_markets = [];
    private int $disc_number = 0;
    private int $duration_ms = 0;
    private bool $explicit = false;
    private array $external_ids = [];
    private array $external_urls = [];
    private string $href = '';
    private bool $is_local = false;
    private string $name = '';
    private int $popularity = 0;
    private string $preview_url = '';
    private int $track_number = 0;
    private string $type = '';
    private string $uri = '';


    public function __construct(Spotify $spotify, string $trackId, array $data = [])
    {
        parent::__construct($spotify);
        $this->spotify = $spotify;
        $this->trackId = $trackId;
        if(!empty($data)) {
            $this->setData($data);
        }
    }

    public function getTrackId(): string
    {
        return $this->trackId;
    }

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
        $response = parent::fetchData($this->trackId);
        $this->setData($response);
        return $this;
    }


    public function setData(array $data): void
    {

        $this->trackId = $data['id'];
        $this->artists = $data['artists'] ?? [];
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
            'trackId' => $this->trackId,
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
