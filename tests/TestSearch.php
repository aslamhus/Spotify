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
            "access_token" => $_ENV['ACCESS_TOKEN'],
            "token_type" => "Bearer",
            "expires_in" => 3600,
            "scope" => $_ENV['SCOPE'],
            "refresh_token" => $_ENV['REFRESH_TOKEN']

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

    // public function testSearchAlbum()
    // {
    //     $search = new Search($this->spotify);
    //     $limit = 5;
    //     $search->exec('Steely Dan', 'album', $limit);
    //     $albumResults = $search->getResultsForType('album');
    //     $firstCount = count($albumResults);
    //     $this->assertTrue($firstCount > 0);
    //     if($albumResults->hasNext()) {
    //         $albumResults->next();
    //         $secondCount = count($albumResults);
    //         $this->assertTrue($secondCount > $firstCount);
    //     }
    //     echo "Pagination: \n ";
    //     // show results by name of tracks
    //     $albums = $albumResults->getItems();
    //     $this->assertTrue(count($albums) === $limit * 2);
    //     $albumNames = [];
    //     foreach($albums as $album) {
    //         $albumNames[] = $album->getName();
    //     }
    //     echo json_encode($albumNames, JSON_PRETTY_PRINT);
    // }

    /**
     * Test search for artist and track
     *
     * TODO: finish this test
     *
     * @return void
     */
    public function testSearchArtistAndTrack()
    {
        $search = new Search($this->spotify);
        $artist = 'Ed Cherry';
        $track = 'Are We There Yet?';
        $query = $artist . ' ' . $track;
        $search->exec($query, 'track', 15);
        $searchResult = $search->getResultsForType('track');
        $trackNames = [];
        $found = null;

        foreach($searchResult->getItems() as $track) {

            $firstArtist = $track->getArtists()[0];
            $this->assertTrue(is_object($firstArtist));
            $album = $track->getAlbum();
            $name = $track->getName();
            // get list of artist names
            $artists = $track->getArtists();
            $artistsNames = [];
            foreach($artists as $artist) {

                $artistNames[] = $artist->getName();
            }
            // push track name into array
            $trackNames[] = $name . ' - ' . implode(', ', $artistNames);
            if($name == $track) {
                $found = $track;
            }
        }
    }




}
