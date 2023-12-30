<?php

namespace Aslamhus\SpotifyClient\User;

use Aslamhus\SpotifyClient\Spotify;
use Aslamhus\SpotifyClient\Interfaces\EntityInterface;

/**
 * User ORM
 *
 * Tack data can be passed in on construct or fetched from Spotify API
 *
 * Note that unlike other entities, the User entity does not require an id
 * since we simply want to get the current user's data
 *
 * @see https://developer.spotify.com/documentation/web-api/reference/get-track
 */
class User extends UserController implements EntityInterface, \JsonSerializable
{
    private Spotify $spotify;
    private string $userId = '';
    private string $display_name = '';
    private string $country = '';
    private string $email = '';
    private array $explicit_content = [];
    private array $external_urls = [];
    private array $followers = [];
    private string $href = '';
    private array $images = [];
    private string $product = '';
    private string $type = '';
    private string $uri = '';

    public function __construct(Spotify $spotify, array $data = [])
    {
        parent::__construct($spotify);
        $this->spotify = $spotify;
        if(!empty($data)) {
            $this->setData($data);
        }
    }

    public function getData(): self
    {
        $data = parent::fetchData();
        $this->setData($data);
        return $this;
    }

    public function setData(array $data): void
    {
        $this->userId = $data['id'] ?? '';
        $this->display_name = $data['display_name'] ?? '';
        $this->country = $data['country'] ?? '';
        $this->email = $data['email'];
        $this->explicit_content = $data['explicit_content'] ?? [];
        $this->external_urls = $data['external_urls'] ?? [];
        $this->followers = $data['followers'] ?? [];
        $this->href = $data['href'] ?? '';
        $this->images = $data['images'] ?? [];
        $this->product = $data['product'] ?? '';
        $this->type = $data['type'] ?? '';
        $this->uri = $data['uri'] ?? '';
    }

    public function jsonSerialize(): mixed
    {
        return [
            'userId' => $this->userId,
            'display_name' => $this->display_name,
            'country' => $this->country,
            'email' => $this->email,
            'explicit_content' => $this->explicit_content,
            'external_urls' => $this->external_urls,
            'followers' => $this->followers,
            'href' => $this->href,
            'images' => $this->images,
            'product' => $this->product,
            'type' => $this->type,
            'uri' => $this->uri,
        ];
    }
}
