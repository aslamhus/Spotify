<?php


require_once __DIR__ . '/config.php';

use PHPUnit\Framework\TestCase;

use Aslamhus\SpotifyClient\Auth\AuthCodeToken;
use Aslamhus\SpotifyClient\Auth\ClientCredentials;
use Aslamhus\SpotifyClient\Auth\AuthorizationCode;
use Aslamhus\SpotifyClient\SpotifyClient;
use Aslamhus\SpotifyClient\Spotify;
use Aslamhus\SpotifyClient\Artist\Artist;
use Aslamhus\SpotifyClient\Album\Album;
use Aslamhus\SpotifyClient\Auth\AccessToken;
use Aslamhus\SpotifyClient\Interfaces\AuthorizationInterface;
use Aslamhus\SpotifyClient\User\User;
use Aslamhus\SpotifyClient\Playlist\Playlist;
use Aslamhus\SpotifyClient\Search\Search;
use Aslamhus\SpotifyClient\Track\Tracks;

class TestSearch extends TestCase
{
    private AuthorizationInterface $clientCredentials;
    private SpotifyClient $client;
    private Spotify $spotify;
    private AccessToken $refreshableToken;

    public function __construct()
    {
        parent::__construct();
        $this->client = new SpotifyClient($_ENV['SPOTIFY_CLIENT_ID'], $_ENV['SPOTIFY_CLIENT_SECRET']);
        $this->clientCredentials = new ClientCredentials($this->client);
        $this->spotify = new Spotify($this->clientCredentials->getToken(), $this->client);
        $this->refreshableToken =   new AccessToken([
            "access_token" => "BQCcroMuU4jLgxEEPqAwfdw36RuTi0aOm_t6Uhm_AW_F_csv7umypB4O2IWo9_Z5RTByMKP5UrDx-fWo_zQlp2FFOpCNqqSicIAIzZd5stqqg2gi7ED_JN6yHk7sgz1pqWzUVm3ISLg2jN6KeTyDF_cmegT3ycXzgokQgGmCAtb98xdmYZ_cBgKtGI7ywdtTa5PxKKymR5Ezc-fRLXL3zUVcBMZCJGBffi9wmT74ElT7TCrvUocROnYOL26jBbBpORekfpXSnqhWVi7EQbwI_6m9dwrf-Jto",
            "token_type" => "Bearer",
            "expires_in" => 3600,
            "scope" => "playlist-read-private playlist-read-collaborative ugc-image-upload playlist-modify-private playlist-modify-public user-read-email user-read-private",
            "refresh_token" => "AQB_Rvfmm-ahUwh8WLkucD3bRxMGdgYMBNQc46fRaB6ik9qKOBqC10ByzZ2a8nKlAxNr2R2sRNORVXVmUbYe4HLG5qzo5HeSEY7eyCyALkfejCeIa7Q3q3ZmpJ4jRpsOLh0"

        ]);
    }



    // public function testGetAllSearchResults()
    // {
    //     $search = new Search($this->spotify);
    //     $search->exec('Steely Dan', 'artist,track', 5);
    //     $searchResults = $search->getAllResults();
    //     $this->assertEquals(['artists','tracks'], array_keys($searchResults));
    //     $tracksItems = $searchResults['tracks']->getItems();
    //     $this->assertEquals(Tracks::class, get_class($tracksItems));

    // }

    // public function testGetNextResultsWithWhileLoop()
    // {
    //     $search = new Search($this->spotify);
    //     $search->exec('Steely Dan', 'artist,track', 50);
    //     $trackResults = $search->getResultsForType('track');
    //     $firstCount = count($trackResults);
    //     $this->assertTrue($firstCount > 0);
    //     $breakLimit = 3;
    //     $i = 0;
    //     while($trackResults->hasNext()) {
    //         $trackResults->next();
    //         $secondCount = count($trackResults);
    //         $this->assertTrue($secondCount > $firstCount);

    //         if($i > $breakLimit) {
    //             break;
    //         }
    //         $i++;

    //     }
    //     echo "Pagination: \n ";
    //     print_r($trackResults->getPagination());
    //     // show results by name of tracks
    //     $tracks = $trackResults->getItems();
    //     // echo json_encode($tracks->getTrackNames(), JSON_PRETTY_PRINT);
    // }

    // public function testGetNextResultsForArtist()
    // {
    //     $search = new Search($this->spotify);
    //     $limit = 5;
    //     $search->exec('Steely Dan', 'artist', $limit);
    //     $artistResults = $search->getResultsForType('artist');
    //     $firstCount = count($artistResults);
    //     $this->assertTrue($firstCount > 0);
    //     if($artistResults->hasNext()) {
    //         $artistResults->next();
    //         $secondCount = count($artistResults);
    //         $this->assertTrue($secondCount > $firstCount);
    //     }
    //     echo "Pagination: \n ";
    //     // show results by name of tracks
    //     $artists = $artistResults->getItems();
    //     $this->assertTrue(count($artists) === $limit * 2);
    // }

    public function testSearchAlbum()
    {
        $search = new Search($this->spotify);
        $limit = 5;
        $search->exec('Steely Dan', 'album', $limit);
        $albumResults = $search->getResultsForType('album');
        $firstCount = count($albumResults);
        $this->assertTrue($firstCount > 0);
        if($albumResults->hasNext()) {
            $albumResults->next();
            $secondCount = count($albumResults);
            $this->assertTrue($secondCount > $firstCount);
        }
        echo "Pagination: \n ";
        // show results by name of tracks
        $albums = $albumResults->getItems();
        $this->assertTrue(count($albums) === $limit * 2);
        $albumNames = [];
        foreach($albums as $album) {
            $albumNames[] = $album->getName();
        }
        echo json_encode($albumNames, JSON_PRETTY_PRINT);
    }




}
