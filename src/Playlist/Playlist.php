<?php

namespace Aslamhus\SpotifyClient\Playlist;

use Aslamhus\SpotifyClient\Interfaces\EntityInterface;
use Aslamhus\SpotifyClient\Spotify;
use Aslamhus\SpotifyClient\Track\Track;
use Aslamhus\SpotifyClient\Track\Tracks;
use Aslamhus\SpotifyClient\User\User;
use Aslamhus\SpotifyClient\Track\PaginatedTracks;

/**
 * Playlist ORM
 *
 */
class Playlist extends PlaylistController implements EntityInterface, \JsonSerializable
{
    private Spotify $spotify;
    private User $user;
    private string $id;
    private bool $collaborative = false;
    private string $description = '';
    private array $external_urls = [];
    private array $followers = [];
    private string $href = '';
    private array $images = [];
    private string $name = '';
    private bool $public = false;
    private string $snapshot_id = '';
    public PaginatedTracks $tracks;
    private string $type = '';
    private string $uri = '';
    public const COVER_IMG_MAX_FILE_SIZE = 256000;


    /**
     * Constructor
     *
     * @param Spotify $spotify
     * @param array [$data] - optional data to populate the playlist object
     */
    public function __construct(Spotify $spotify, User $user, array $data = [])
    {
        parent::__construct($spotify);
        $this->spotify = $spotify;
        $this->user = $user;
        // if the user object is empty, get the user data
        if(empty($this->user->getId())) {
            $this->user = $this->user->getData();
        }
        // if the playlist data is passed in, set the data
        if(!empty($data)) {
            $this->setData($data);
        }
    }

    /**
     * Set data with remote or local data.
     *
     * @param array $data
     * @return void
     */
    public function setData(array $data): void
    {

        $this->id = $data['id'] ?? '';
        $this->collaborative = $data['collaborative'] ?? false;
        $this->description = $data['description'] ?? '';
        $this->external_urls = $data['external_urls'] ?? [];
        $this->followers = $data['followers'] ?? [];
        $this->href = $data['href'] ?? '';
        $this->images = $data['images'] ?? [];
        $this->name = $data['name'] ?? '';
        $this->public = $data['public'] ?? false;
        $this->snapshot_id = $data['snapshot_id'] ?? '';
        if(isset($data['tracks'])) {
            $this->tracks = new PaginatedTracks($this->spotify, $data['tracks']);
        }

        $this->type = $data['type'] ?? '';
        $this->uri = $data['uri'] ?? '';

    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getTracks(): PaginatedTracks
    {
        return $this->tracks;
    }

    public function getDetails(): array
    {
        return [
            'id' => $this->id,
            'collaborative' => $this->collaborative,
            'description' => $this->description,
            'external_urls' => $this->external_urls,
            'followers' => $this->followers,
            'href' => $this->href,
            'images' => $this->images,
            'name' => $this->name,
            'public' => $this->public,
            'snapshot_id' => $this->snapshot_id,
            'type' => $this->type,
            'uri' => $this->uri,
        ];
    }

    /**
     * Get total tracks
     *
     * @return integer - the number of total tracks in the playlist (not just the tracks in the current page)
     */
    public function getTotalTracks(): int
    {
        return $this->tracks->getTotal();
    }

    public function getTracksPaginationInfo(): array
    {
        return $this->tracks->getPagination();
    }

    /**
     * Gets the playlist data
     *
     * Populates the playlist object with data from the Spotify API
     * including PaginatedTracks object
     *
     * @return Playlist
     */
    public function getData(): self
    {
        if(empty($this->id)) {
            throw new \Exception('Cannot get playlist data, playlist id is empty');
        }
        // get playlist data for playlist with id $this->id
        $response =  $this->fetchData($this->id);
        // parse the response and set the data
        $this->setData($response);
        // return the playlist object
        return $this;
    }





    /**
     * Update cover image
     *
     * Note: requires permission scope:
     * 1. ugc-image-upload
     * 2. playlist-modify-public
     * 3. playlist-modify-private
     *
     * @param string $filePath - the path to the image file
     * @return ?array - the response from the Spotify API
     **/
    public function updateCoverImage(string $filePath): ?array
    {
        // get file
        if(!is_file($filePath)) {
            throw new \Exception("Invalid file path: $filePath");
        }
        // max file size check
        if(filesize($filePath) > self::COVER_IMG_MAX_FILE_SIZE) {
            throw new \Exception("File size is above maximum (" . self::COVER_IMG_MAX_FILE_SIZE . "): " . filesize($filePath));
        }
        // get file contents and encode to base64
        $file = base64_encode(file_get_contents($filePath));
        return parent::addCustomPlaylistCoverImage($this->id, $file);
    }


    /**
     * Get cover image
     *
     * @return ?array - A multidimensional array where the first value is the cover image array
     * e.g. [[ 'url' => '', 'width' => 0, 'height' => 0]]
     */
    public function getCoverImage(): ?array
    {
        return parent::getPlaylistCoverImage($this->id);
    }



    /**
     * Create a playlist
     *
     * (Factory pattern)
     *
     * @param Spotify $spotify - the Spotify object
     * @param User $user - the user object
     * @param array $options {
     *      @var string $name - the name of the playlist
     *      @var string [$description] - the description of the playlist
     *      @var bool [$public] - whether the playlist is public or not (default: false)
     * }
     * @return Playlist - the newly created playlist
     */
    public static function create(Spotify $spotify, User $user, $options): Playlist
    {
        $name = $options['name'] ?? '';
        if(empty($name)) {
            throw new \Exception('Cannot create playlist, name is empty');
        }
        $description = $options['description'] ?? '';
        $public = $options['public'] ?? false;
        $data = parent::createPlaylistForUser($spotify, $user, $name, $description, $public);
        // parse the response and set the data
        return new Playlist($spotify, $user, $data);
    }

    /**
     * Change details
     *
     * @param array $options {
     * @var string [$name] - the name of the playlist
     * @var string [$description] - the description of the playlist
     * @var bool [$public] - whether the playlist is public or not (default: false)
     * }
     * @return ?array
     */
    public function changeDetails(array $options): ?array
    {
        $this->checkTracksExist();
        $response =  parent::changeDetailsForPlaylist($this->id, $options);
        // update the local model
        $this->name = $options['name'] ?? $this->name;
        $this->description = $options['description'] ?? $this->description;
        $this->public = $options['public'] ?? $this->public;
        return $response;
    }


    /**
     * Add Track
     *
     * @param Track $track - the track to add to the playlist
     * @param integer $position - the position in the playlist to add the track
     * @return array|null
     */
    public function addTrack(Track $track, int $position = 0): ?array
    {
        if(empty($this->id)) {
            throw new \Exception('Cannot add items to playlist, playlist id is empty');
        }
        $this->checkTracksExist();
        // convert the trackUris array to an array of uris
        $itemsToAdd = [$track->getUri()];
        // make request
        $request =   parent::addTracksToPlaylist($this->id, $itemsToAdd, $position);
        // update the snapshot_id
        $this->snapshot_id = $request['snapshot_id'] ?? '';
        // update the local model manually
        $this->tracks->addTrack($track, $position);
        return $request;
    }

    /**
     * Add tracks to playlist
     *
     *
     * @param Tracks $tracks - the tracks to add to the playlist
     * @param integer [$position] - the position in the playlist to add the tracks
     * @return ?array
     */
    public function addTracks(Tracks $tracks, int $position = 0): ?array
    {
        if(empty($this->id)) {
            throw new \Exception('Cannot add items to playlist, playlist id is empty');
        }
        $this->checkTracksExist();
        // convert the trackUris array to an array of uris
        $itemsToAdd = [];

        foreach($tracks as $track) {
            $itemsToAdd[] = $track->getUri();
        }

        // make request
        $response =   parent::addTracksToPlaylist($this->id, $itemsToAdd, $position);
        // update the local model
        foreach($tracks as $track) {
            $this->tracks->addTrack($track, $position);
            $position++;
        }
        return $response;

    }

    /**
     * Remove tracks from playlist
     *
     * @param Tracks $tracks - an array of Track objects
     * @return array|null
     */
    public function removeTracks(Tracks $tracks): ?array
    {
        if(empty($this->id)) {
            throw new \Exception('Cannot remove items from playlist, playlist id is empty');
        }
        $this->checkTracksExist();
        // convert the trackUris array to an array of objects
        $itemsToRemove = [];
        foreach($tracks->toArray() as $track) {
            /** @var Track  */
            $itemsToRemove[] = ['uri' => $track->getUri()];

        }
        // remove items from playlist
        $response =  parent::removeTracksFromPlaylist($this->id, $itemsToRemove, $this->snapshot_id);
        // update the local model
        $this->tracks->removeTracks($tracks);
        return $response;
    }


    /**
     * Remove all tracks from playlist
     *
     * @return array|null
     */
    public function clearPlaylist(): ?array
    {
        $this->checkTracksExist();
        // remove all tracks from playlist
        $response =  $this->removeTracks($this->tracks->toTracks());
        // update local model
        $this->tracks = new PaginatedTracks($this->spotify, []);
        return $response;
    }


    public function reorderTracks(int $rangeStart, int $insertBefore, int $rangeLength = 1): ?array
    {
        $this->checkTracksExist();
        // reorder tracks in playlist
        $response = parent::updatePlaylistItems($this->id, [
            'range_start' => $rangeStart,
            'insert_before' => $insertBefore,
            'range_length' => $rangeLength,
        ]);
        // update the local model with remote data
        $this->tracks->reorderTracks($rangeStart, $rangeLength, $insertBefore);
        return $response;

    }

    /**
     * Replace Tracks
     *
     * Replaces tracks starting at position for the length of the given tracks.
     * This will clear the playlist
     *
     * @param Tracks $tracks
     * @param integer $position
     * @return array|null
     */
    public function replaceTracks(Tracks $tracks): ?array
    {

        // check that the number of tracks to replace is less than the number of tracks in the playlist
        if(count($tracks) > $this->tracks->getTotal()) {
            throw new \Exception('Cannot replace tracks, number of tracks to replace is greater than the number of tracks in the playlist');
        }
        // replace tracks in playlist
        $response =  parent::updatePlaylistItems($this->id, [], $tracks);
        // update the local model
        // remove all tracks from playlist
        $this->tracks = new PaginatedTracks($this->spotify, []);
        // add the new tracks
        foreach($tracks as $track) {
            $this->tracks->addTrack($track);
        }


        return $response;
    }

    /**
     * Check tracks exist
     *
     * @return bool
     */
    private function checkTracksExist(): bool
    {
        if(empty($this->tracks)) {
            throw new \Exception('Cannot perform action, no tracks exist or they have not been loaded');
        }
        return true;
    }




    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'collaborative' => $this->collaborative,
            'description' => $this->description,
            'external_urls' => $this->external_urls,
            'followers' => $this->followers,
            'href' => $this->href,
            'images' => $this->images,
            'name' => $this->name,
            'public' => $this->public,
            'snapshot_id' => $this->snapshot_id,
            'tracks' => $this->tracks->jsonSerialize(),
            'type' => $this->type,
            'uri' => $this->uri,
        ];
    }


}
