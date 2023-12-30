<?php

namespace Aslamhus\SpotifyClient\Playlist;

use Aslamhus\SpotifyClient\Spotify;

/**
 * Playlist ORM
 *
 * Note: make sure to add categories to playlists so that
 * users can filter their playlists by category
 *
 *
 */
class Playlist extends PlaylistController
{
    private Spotify $spotify;
    private string $id;

    /**
     * Constructor
     *
     * @param Spotify $spotify
     * @param string $id
     */
    public function __construct(Spotify $spotify, string $id = '')
    {
        parent::__construct($spotify);
        $this->spotify = $spotify;
        $this->id = $id;
    }

    /**
     * Gets the playlist data
     *
     * Populates the playlist object with data from the Spotify API
     *
     * @return array|null
     */
    public function getData()
    {
        // return $this->fetchData($playlistId);
    }

    public static function getPlaylists(Spotify $spotify): array
    {
        // get playlists for user
        return [];
    }

    public static function getPlaylistsByCategory(Spotify $spotify, string $categoryId): array
    {
        // get playlists for category
        return [];
    }

    public function getCoverImage(): string
    {
        // get cover image for playlist
        return '';
    }




    /**
     * Create a playlist
     *
     * @param Spotify $spotify
     * @param array $options
     *
     * @return Playlist
     */
    public static function create(Spotify $spotify, array $options): self
    {

        // create, then set the id
        $url = "https://api.spotify.com/v1/users/{user_id}/playlists";
        $id = '1234';
        return new Playlist($spotify, $id);
    }

    public function addItems(array $items): void
    {
        // add tracks to playlist
    }

    public function removeItems(array $items): void
    {
        // remove tracks from playlist
    }

    public function changeDetails(array $options): void
    {
        // change playlist details
    }

    public function addPlaylistCoverImage(string $image): void
    {
        // add playlist cover image
    }


}
