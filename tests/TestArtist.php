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

class TestArtist extends TestCase
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

    // public function testArtist()
    // {
    //     $artist = new Artist($this->spotify, '4Z8W4fKeB5YxbusRsdQVPb');
    //     // $artist->getData();
    //     echo json_encode($artist, JSON_PRETTY_PRINT);
    // }

    // public function testGetArtistTracks()
    // {
    //     $artist = new Artist($this->spotify, '4Z8W4fKeB5YxbusRsdQVPb');
    //     $tracks = $artist->getAlbums();
    //     echo json_encode($tracks, JSON_PRETTY_PRINT);
    // }

    public function testGetArtistSearchResult()
    {
        $search = new Search($this->spotify);
        $search->exec('Steely Dan', 'artist', 5);
        $searchResults = $search->getResultsForType('artist');
        $firstCount = count($searchResults);
        echo "Search results found: " . $firstCount . "\n";
        $this->assertTrue($firstCount > 0);
        echo "Has next results? " . ($searchResults->hasNext() ? 'yes' : 'no') . "\n";
        echo "\n";
        foreach($searchResults as $result) {

            $names[] = $result->getName();
        }
        echo json_encode($names, JSON_PRETTY_PRINT);
        while($searchResults->hasNext()) {
            $searchResults->next();
            $secondCount = count($searchResults);
            echo "\n\nNext results retrieved. Total search results now: " . $secondCount . "\n";
            $this->assertTrue($secondCount > $firstCount);

        }
        echo "\n";
        // display all artist names
        $names = [];
        foreach($searchResults as $result) {
            $names[] = $result->getName();
        }
        echo json_encode($names, JSON_PRETTY_PRINT);
        $this->assertTrue(count($names) === $secondCount);
    }




}
