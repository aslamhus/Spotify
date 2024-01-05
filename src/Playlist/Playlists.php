<?php

namespace Aslamhus\SpotifyClient\Playlist;

use Aslamhus\SpotifyClient\Interfaces\EntityInterface;
use Aslamhus\SpotifyClient\Spotify;
use Aslamhus\SpotifyClient\Track\Track;
use Aslamhus\SpotifyClient\Track\Tracks;
use Aslamhus\SpotifyClient\User\User;
use Aslamhus\SpotifyClient\Playlist\Playlist;
use Aslamhus\SpotifyClient\Pagination\PaginationController;

/**
 * Playlists ORM
 *
 */
class Playlists extends PaginationController implements EntityInterface, \JsonSerializable, \IteratorAggregate, \Countable, \ArrayAccess
{
    private array $playlists = [];
    protected Spotify $spotify;
    private User $user;



    /**
     * Constructor
     *
     * @param Spotify $spotify
     * @param array [$data] - optional data to populate the playlist object
     * @param integer [$limit] - optional limit for pagination
     * @param integer [$offset] - optional offset for pagination
     */
    public function __construct(Spotify $spotify, User $user, array $data = [], int $limit = 20, int $offset = 0)
    {
        parent::__construct($spotify, $limit, $offset);
        $this->spotify = $spotify;
        $this->user = $user;
        // if the playlist data is passed in, set the data
        if(!empty($data)) {
            $this->setData($data);
        }
    }

    public function count(): int
    {
        return parent::getTotal();
    }


    public function setData(array $data): void
    {
        // set the playlists
        foreach($data as $playlistData) {
            $playlist = new Playlist($this->spotify, $this->user, $playlistData);
            $this->playlists[] = $playlist;
        }
    }

    public function getData(): self
    {
        $userId = $this->user->getId();
        if(empty($userId)) {
            throw new \Exception('User ID is required to get playlists. Please set the user ID or load the user data');
        }
        $items = parent::fetchData("/users/{$userId}/playlists");
        $this->setData($items);
        return $this;
    }

    public function getName(): string
    {
        return 'Playlists';
    }

    public function getId(): string
    {
        return '';
    }

    /**
     * Get playlists
     *
     * @return ?Array<Playlist>
     */
    public function getPlaylists(): array
    {
        return $this->playlists;
    }






    public function jsonSerialize(): array
    {
        $playlists = [];
        foreach($this->playlists as $playlist) {
            $playlists[] = $playlist->jsonSerialize();
        }
        return [
            'playlists' => $playlists,
        ];
    }

    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->playlists);
    }

    public function offsetExists($offset): bool
    {
        return isset($this->playlists[$offset]);
    }

    public function offsetGet($offset): ?Playlist
    {
        return $this->playlists[$offset] ?? null;
    }

    public function offsetSet($offset, $value): void
    {
        if (is_null($offset)) {
            $this->playlists[] = $value;
        } else {
            $this->playlists[$offset] = $value;
        }
    }

    public function offsetUnset($offset): void
    {
        unset($this->playlists[$offset]);
    }


}
