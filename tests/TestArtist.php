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
            "access_token" => "BQCcroMuU4jLgxEEPqAwfdw36RuTi0aOm_t6Uhm_AW_F_csv7umypB4O2IWo9_Z5RTByMKP5UrDx-fWo_zQlp2FFOpCNqqSicIAIzZd5stqqg2gi7ED_JN6yHk7sgz1pqWzUVm3ISLg2jN6KeTyDF_cmegT3ycXzgokQgGmCAtb98xdmYZ_cBgKtGI7ywdtTa5PxKKymR5Ezc-fRLXL3zUVcBMZCJGBffi9wmT74ElT7TCrvUocROnYOL26jBbBpORekfpXSnqhWVi7EQbwI_6m9dwrf-Jto",
            "token_type" => "Bearer",
            "expires_in" => 3600,
            "scope" => "playlist-read-private playlist-read-collaborative ugc-image-upload playlist-modify-private playlist-modify-public user-read-email user-read-private",
            "refresh_token" => "AQB_Rvfmm-ahUwh8WLkucD3bRxMGdgYMBNQc46fRaB6ik9qKOBqC10ByzZ2a8nKlAxNr2R2sRNORVXVmUbYe4HLG5qzo5HeSEY7eyCyALkfejCeIa7Q3q3ZmpJ4jRpsOLh0"

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
