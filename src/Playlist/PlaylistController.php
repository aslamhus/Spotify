<?php

namespace Aslamhus\SpotifyClient\Playlist;

use Aslamhus\SpotifyClient\Interfaces\EntityControllerInterface;
use Aslamhus\SpotifyClient\Spotify;
use Aslamhus\SpotifyClient\User\User;
use Aslamhus\SpotifyClient\Track\Tracks;

class PlaylistController implements EntityControllerInterface
{
    private Spotify $spotify;

    public function __construct(Spotify $spotify)
    {
        $this->spotify = $spotify;
    }

    public function fetchData(string $playlistId = ''): array
    {
        return $this->spotify->get("playlists/$playlistId", [
            'offset' => 0,
            'limit' => 1
        ]);
    }

    /**
     * Create playlist for user
     *
     * @param Spotify $spotify
     * @param User $user
     * @param string $name
     * @param string $description
     * @param boolean $public
     *
     * @return ?array
     */
    protected static function createPlaylistForUser(Spotify $spotify, User $user, string $name, string $description, $public = false): ?array
    {
        $data = [
             'name' => $name,
             'description' => $description,
             'public' => $public
         ];
        $userId = $user->getId();
        return $spotify->post("users/$userId/playlists", $data);
    }


    /**
     * Change details for playlist
     *
     * !impoortant: the spotify api can sometimes take a while
     * to update the playlist details, so there may be a discrepancy
     * between local and remote data after change the details.
     *
     * @param string $playlistId
     * @param array $options {
     *      @var string [$name] - the name of the playlist
     *      @var string [$description] - the description of the playlist
     *      @var bool [$public] - whether the playlist is public or not (default: false)
     * }
    *
     * @return array|null
     */
    protected function changeDetailsForPlaylist(string $playlistId, array $options): ?array
    {
        if(!isset($options['name']) && !isset($options['description']) && !isset($options['public'])) {
            throw new \Exception('Cannot change details for playlist, options are empty');
        }
        return $this->spotify->put("playlists/$playlistId", $options);
    }

    /**
     * Add tracks to playlist
     *
     * ### Schema
     * {  "uris": [  "string"  ], "position": 0}
     *
     * @param string $playlistId
     * @param array $trackUris - array of track uris [ 'spotify:track:4iV5W9uYEdYUVa79Axb7Rh']
     * @param integer [$position] - the position in the playlist to add the tracks
     * For example, to insert the tracks in the first position: position=0;
     * to insert the tracks in the third position: position=2.
     * If omitted, the tracks will be appended to the playlist.
     * @return ?array
     */
    protected function addTracksToPlaylist(string $playlistId, array $trackUris, ?int $position = null)
    {
        if(empty($trackUris)) {
            throw new \Exception('Cannot add tracks to playlist, trackUris is empty');
        }
        $data = [
            'uris' => $trackUris
        ];
        if(!empty($position)) {
            $data['position'] = $position;
        }
        return $this->spotify->post("playlists/$playlistId/tracks", $data);
    }

    /**
     * Remove tracks from a playlist
     *
     * ### Schema
     *
     * {
     * "tracks": [ { "uri": "string" }  ], "snapshot_id": "string"} ]
     *
     * @see https://developer.spotify.com/documentation/web-api/reference/remove-tracks-playlist
     *
     * @param string $playlistId
     * @param array $trackUris - array of objects with track uri [ { uri: 'spotify:track:4iV5W9uYEdYUVa79Axb7Rh' } ]
     * @param string $snapshotId - the snapshot id of the playlist.
     * The playlist's snapshot ID against which you want to make the changes.
     * The API will validate that the specified items exist and in the specified positions
     * and make the changes, even if more recent changes have been made to the playlist.
     * @return ?array
     */
    protected function removeTracksFromPlaylist(string $playlistId, array $trackUris, string $snapshotId)
    {
        if(empty($trackUris)) {
            throw new \Exception('Cannot remove tracks from playlist, trackUris is empty');
        }
        $data = [
            'tracks' => $trackUris,
            'snapshot_id' => $snapshotId
        ];
        return $this->spotify->delete("playlists/$playlistId/tracks", $data);
    }


    /**
     * Get Playlists for logged in User
     *
     * By default a limit of 50 playlists are returned
     *
     * @param User $user
     * @param integer $offset - the index of the first playlist to return
     * @param integer $limit - the maximum number of playlists to return (default: 50, minimum: 1, maximum: 50)
     * @return array
     */
    protected function getPlaylistsForUser(User $user, int $offset = 0, $limit = 50): array
    {
        if(empty($user->getId())) {
            throw new \Exception('Cannot get playlists for user, user id is empty');
        }
        $userId = $user->getId();
        return $this->spotify->get("users/$userId/playlists", [
            'offset' => $offset,
            'limit' => $limit
        ]);
    }

    protected function addCustomPlaylistCoverImage(string $playlistId, $file): ?array
    {
        $data = $file;
        $headers = [
            'Content-Type' => 'image/jpeg'
        ];
        return $this->spotify->put("playlists/$playlistId/images", $data, $headers, 'body');
    }

    protected function getPlaylistCoverImage(string $playlistId): ?array
    {
        return $this->spotify->get("playlists/$playlistId/images");
    }

    /**
     * Update playlist items (Reorder or replace)
     *
     * To reorder, specify the range of the items and where to insert
     * To replace, specify the range of the items and specify the tracks to replace them with
     * To clear a playlist, specify the range of the items and leave the tracks array empty
     *
     *
     * @see https://developer.spotify.com/documentation/web-api/reference/reorder-or-replace-playlists-tracks
     *
     * @param string $playlistId
     * @param string $snapshotId
     * @param array [$items]
     * @param array [$range] {
     *      @var int range_start - the position of the first item to be reordered
     *      @var int range_length - the amount of items to be reordered
     *      @var int insert_before - the position where the items should be inserted
     * }
     * @param Tracks $tracks - the tracks to replace the items with
     * @return array|null
     */
    protected function updatePlaylistItems(string $playlistId, array $range = [], ?Tracks $tracks = null): ?array
    {
        $data = [];
        // add range
        $data['range_start'] = $range['range_start'] ?? 0;
        $data['insert_before'] = $range['insert_before'] ?? 0;
        $data['range_length'] = $range['range_length'] ?? 1;
        // build query string with tracks
        $queryString = "";
        if(!empty($tracks)) {

            $uris = [];
            foreach($tracks->toArray() as $track) {
                $uris[] = $track->getUri();
            }
            $queryString = "?uris=" . implode(',', $uris);
        }
        return $this->spotify->put("playlists/$playlistId/tracks$queryString", $data);
    }

}
