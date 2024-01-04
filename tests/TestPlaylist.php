<?php

require_once __DIR__ . '/config.php';

use PHPUnit\Framework\TestCase;

use Aslamhus\SpotifyClient\Interfaces\AuthorizationInterface;
use Aslamhus\SpotifyClient\Auth\AccessToken;
use Aslamhus\SpotifyClient\Auth\ClientCredentials;
use Aslamhus\SpotifyClient\Auth\AuthorizationCode;
use Aslamhus\SpotifyClient\SpotifyClient;
use Aslamhus\SpotifyClient\Spotify;
use Aslamhus\SpotifyClient\User\User;
use Aslamhus\SpotifyClient\Playlist\Playlist;
use Aslamhus\SpotifyClient\Playlist\Playlists;
use Aslamhus\SpotifyClient\Track\Track;
use Aslamhus\SpotifyClient\Track\Tracks;

class TestPlaylist extends TestCase
{
    private Spotify $spotify;
    private User $user;
    private Playlist $playlist;

    public function __construct()
    {
        parent::__construct();
        $client = new SpotifyClient($_ENV['SPOTIFY_CLIENT_ID'], $_ENV['SPOTIFY_CLIENT_SECRET']);
        $token =   new AccessToken([
            "access_token" => "BQCcroMuU4jLgxEEPqAwfdw36RuTi0aOm_t6Uhm_AW_F_csv7umypB4O2IWo9_Z5RTByMKP5UrDx-fWo_zQlp2FFOpCNqqSicIAIzZd5stqqg2gi7ED_JN6yHk7sgz1pqWzUVm3ISLg2jN6KeTyDF_cmegT3ycXzgokQgGmCAtb98xdmYZ_cBgKtGI7ywdtTa5PxKKymR5Ezc-fRLXL3zUVcBMZCJGBffi9wmT74ElT7TCrvUocROnYOL26jBbBpORekfpXSnqhWVi7EQbwI_6m9dwrf-Jto",
            "token_type" => "Bearer",
            "expires_in" => 3600,
            "scope" => "playlist-read-private playlist-read-collaborative ugc-image-upload playlist-modify-private playlist-modify-public user-read-email user-read-private",
            "refresh_token" => "AQB_Rvfmm-ahUwh8WLkucD3bRxMGdgYMBNQc46fRaB6ik9qKOBqC10ByzZ2a8nKlAxNr2R2sRNORVXVmUbYe4HLG5qzo5HeSEY7eyCyALkfejCeIa7Q3q3ZmpJ4jRpsOLh0"

        ]);
        $this->spotify = new Spotify($token, $client);
        $this->user = new User($this->spotify);
        $this->user->getData();
        // $this->playlist = new Playlist($this->spotify, $this->user, ['id' => '2YsnoSxgTGvfQGLL1YwwYQ']);
    }

    // public function testGetPlaylistData()
    // {
    //     $playlistJson = json_encode($this->playlist, JSON_PRETTY_PRINT);
    //     // echo $playlistJson;
    //     $this->assertNotEmpty($playlistJson);
    // }

    // public function testCreatePlaylist()
    // {

    //     $client = new SpotifyClient($_ENV['SPOTIFY_CLIENT_ID'], $_ENV['SPOTIFY_CLIENT_SECRET']);
    //     $spotify = new Spotify($this->refreshableToken, $client);
    //     $user = new User($spotify);
    //     $playlist = new Playlist($spotify, $user);
    //     $playlist->create('Test Playlist', 'Test description');
    //     // $this->assertArrayHasKey('userId', $user->getData());
    //     echo json_encode($playlist, JSON_PRETTY_PRINT);
    // }

    // public function testCreatePlaylist()
    // {

    //     $client = new SpotifyClient($_ENV['SPOTIFY_CLIENT_ID'], $_ENV['SPOTIFY_CLIENT_SECRET']);
    //     $spotify = new Spotify($this->refreshableToken, $client);
    //     $user = new User($spotify);
    //     $playlist = new Playlist($spotify, $user, ['id' => '2YsnoSxgTGvfQGLL1YwwYQ']);
    //     $playlist->addTracks(['spotify:track:5xxumuSMspEXt19CGfeiD2', 'spotify:track:2GcpSjLEU4mlte7cGUSagA']);
    //     // $this->assertArrayHasKey('userId', $user->getData());
    //     echo json_encode($playlist, JSON_PRETTY_PRINT);
    // }

    // public function testAddTrack()
    // {
    // $track = new Track($this->spotify, '5xxumuSMspEXt19CGfeiD2');
    // $track->getData();
    // $this->playlist->addTracks([$track], 1);
    // echo json_encode($track, JSON_PRETTY_PRINT);
    // }

    // public function testRemoveTrack()
    // {

    //     $tracksToDelete = $this->playlist->tracks->findTracksByName('Society Al');
    //     if($tracksToDelete) {
    //         $this->playlist->removeTracks($tracksToDelete);
    //     }
    // }

    // public function testGetPlaylists()
    // {
    //     $playlists = new Playlists($this->spotify, $this->user);
    //     $playlists->getData();
    //     $firstPlaylist = $playlists[0];
    //     $firstPlaylist->getData();
    //     $tracks = $firstPlaylist->getTracks();
    //     $pagination = $firstPlaylist->getTracksPaginationInfo();
    //     $this->assertTrue(count($tracks) > 0);
    //     echo "Total tracks loaded: " . count($tracks) . "\n";
    //     echo "Total tracks in playlist: " . $firstPlaylist->getTotalTracks() . "\n";
    //     echo json_encode($pagination, JSON_PRETTY_PRINT);
    //     // get next
    //     echo "\nGetting next page\n";
    //     $firstPlaylist->tracks->next();
    //     echo "Total tracks loaded: " . count($firstPlaylist->getTracks()) . "\n";
    //     echo "Total tracks in playlist: " . $firstPlaylist->getTotalTracks() . "\n";
    //     $pagination = $firstPlaylist->getTracksPaginationInfo();
    //     echo json_encode($pagination, JSON_PRETTY_PRINT);
    // }

    // public function testAddTrackAtSpecificIndex()
    // {
    //     $playlists = new Playlists($this->spotify, $this->user);
    //     $playlists->getData();
    //     // get first playlist
    //     $firstPlaylist = $playlists[0];
    //     // add a Pink Floyd "Speak to me" at 3rd position
    //     $track = new Track($this->spotify, '6rqhFgbbKwnb9MLmUQDhG6');
    //     $position = 8;
    //     $firstPlaylist->addTrack($track, $position);
    //     $firstPlaylist->getData();
    //     // verify that local tracks array has the track at the correct position
    //     $tracks = $firstPlaylist->getTracks()->toArray();
    //     $this->assertEquals($tracks[$position]->getId(), $track->getId());
    //     var_dump($tracks[$position]->getId());
    // }

    // public function testCreateNewPlaylist()
    // {
    //     $playlist = Playlist::create($this->spotify, $this->user, [
    //         'name' => 'My new playlist',
    //         'description' => 'My new description',
    //         'public' => true
    //     ]);

    //     $playlist->changeDetails([
    //         'name' => 'Updated playlist',
    //         'description' => 'My new description 2',
    //         'public' => true
    //     ]);
    // }

    // public function testDeletePlaylist()
    // {
    //     $playlist = new Playlist($this->spotify, $this->user, ['id' => '1ApzGsk2sZUVENwCeKU6fA']);
    //     $result = $playlist->clearPlaylist();
    //     var_dump($result);
    // }

    // public function testReorderTracks()
    // {
    //     $playlist = new Playlist($this->spotify, $this->user, ['id' => '2YsnoSxgTGvfQGLL1YwwYQ']);
    //     $playlist->getData();
    //     // echo json_encode($playlist->tracks->getTrackNames(), JSON_PRETTY_PRINT);
    //     $playlist->reorderTracks(5, 1, 1);
    //     // test the local model has been reordered
    //     $localChanges = [...$playlist->tracks->getTrackNames()];
    //     // get the data from the server
    //     $playlist->getData();
    //     $remoteChanges = [...$playlist->tracks->getTrackNames()];
    //     // check for equality
    //     $this->assertEquals($localChanges, $remoteChanges);

    // }

    // public function testReplaceTracks()
    // {
    //     $tracks = new Tracks([
    //         new Track($this->spotify, '1DMPTsIP3eHHu9cRtthuDe'),
    //         new Track($this->spotify, '1DMPTsIP3eHHu9cRtthuDe')
    //     ]);
    //     $playlist = new Playlist($this->spotify, $this->user, ['id' => '2YsnoSxgTGvfQGLL1YwwYQ']);
    //     $playlist->getData();
    //     // $playlist->addTracks($tracks, 0);
    //     $result = $playlist->replaceTracks($tracks, 3);
    //     $localChanges = [...$playlist->tracks->getTrackIds()];
    //     // get the data from the server
    //     $playlist->getData();
    //     $remoteChanges = [...$playlist->tracks->getTrackIds()];
    //     // print the arrays
    //     echo "\nLocal changes:\n";
    //     echo json_encode($localChanges, JSON_PRETTY_PRINT);
    //     echo "\nRemote changes:\n";
    //     echo json_encode($remoteChanges, JSON_PRETTY_PRINT);
    //     // check for equality
    //     $this->assertEquals($localChanges, $remoteChanges);
    // }

    // public function testChangeDetails()
    // {
    //     $playlist = new Playlist($this->spotify, $this->user, ['id' => '2YsnoSxgTGvfQGLL1YwwYQ']);
    //     $playlist->getData();
    //     $playlist->changeDetails([
    //         'name' => 'Another test',
    //         'description' => 'New test unique id: ' . uniqid(),
    //         'public' => true
    //     ]);
    //     $localDetails = [...$playlist->getDetails()];

    // }

    // public function testUpdateCoverImage()
    // {
    //     $playlist = new Playlist($this->spotify, $this->user, ['id' => '2YsnoSxgTGvfQGLL1YwwYQ']);
    //     $filePath = __DIR__ . '/test.jpg';
    //     $playlist->updateCoverImage($filePath);
    // }

    public function testGetCoverImage()
    {
        $playlist = new Playlist($this->spotify, $this->user, ['id' => '2YsnoSxgTGvfQGLL1YwwYQ']);
        $playlist->getData();
        $image = $playlist->getCoverImage();
        echo json_encode($image[0], JSON_PRETTY_PRINT);
        $this->assertArrayHasKey('url', $image[0]);
    }

    /**
     * Track ids for testing:
     *
     * 1DMPTsIP3eHHu9cRtthuDe
     * 6rqhFgbbKwnb9MLmUQDhG6
     * 1Pt7RPrjEQfzpPA9PS5aZj
     * 3KvKMHMlRj30a7IjHXQVzu
     */
}
