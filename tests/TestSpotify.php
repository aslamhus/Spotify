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

class TestSpotify extends TestCase
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
                    "access_token" => "BQAjVXVqGlNouqgI48iBP1yD5EzzMRjDu6vSCjgZ7Hoq-MkG2yHJl7jZ5YVV4-6eio1GobOyTlwSHdzAoHih0OeHCf8q6zRxlC1eIZ9fVQdWsyz4l_NA9gR3rhBjhHstLYs6dUN_R-zmAj_pTn2VdGyHaoWQ4PQSTL7khoR4OAp-rAwzlqX7Fnh_Ami-6BNvj_ZPqh70W0JwC7l40cJ_OCyEM-ygWETE73qwJoyN4KXGckWmMe4w4ix8H8f6Dc30S9pM7Jjr21Y28FSwjh__y2C8kPdvFQ",
                    "token_type" => "Bearer",
                    "expires_in" => 3600,
                    "scope" => "playlist-read-private playlist-read-collaborative playlist-modify-private playlist-modify-public user-read-email user-read-private",
                    "refresh_token" => "AQC5s1UbnnsPJl8oWV9fVm12ucgDPNH8m3ny9jKN1kVOcQrJpsmuYKkivBQd17_NozaOKILpIqm2e0xSfj7PXcbQDsgmJE5r-nR5AFm_pE9OuLkerbNYMGCNNCQEAxNZ0MA"

                ]);
    }

    // public function testGetAccessToken()
    // {
    //     $accessToken = $this->clientCredentials->getToken();
    //     $this->assertNotEmpty($accessToken->getAccessToken());
    // }

    // public function testGetArtistData()
    // {
    //     $artistData = $this->spotify->get('artists/4Z8W4fKeB5YxbusRsdQVPb');
    //     $this->assertNotEmpty($artistData);
    // }

    // public function testSearchArtist()
    // {
    //     $artistData = $this->spotify->search('Steely Dan', 'artist');
    //     $this->assertNotEmpty($artistData);
    // }

    // public function testGetArtistTracks()
    // {

    //     $tracks = [];
    //     // search for an artist
    //     $artistName = 'Ed Cherry';
    //     $search = $this->spotify->search($artistName, 'artist');
    //     $items = $search['artists']['items'];
    //     $result = false;
    //     // find the artist that exactly matches the search term
    //     foreach ($items as $item) {
    //         if ($item['name'] == $artistName) {
    //             $result = $item;
    //             break;
    //         }
    //     }
    //     $this->assertArrayHasKey('id', $result);
    //     $artistId = $result['id'];
    //     // create Artist object
    //     $artists = new Artist($this->spotify, $artistId);
    //     // get all albums for artist
    //     $albums = $artists->getAlbums();
    //     // push all tracks for each album into $tracks array
    //     foreach($albums as $album) {
    //         $albumName = $album->getName();
    //         $this->assertNotEmpty($albumName);
    //         $albumTracks = $album->getTracks();
    //         foreach($albumTracks as $track) {
    //             $trackName = $track->getName();
    //             $this->assertNotEmpty($trackName);
    //             $tracks[$albumName][$trackName] = $track->getUri();
    //         }
    //     }
    //     echo json_encode($tracks, JSON_PRETTY_PRINT);
    //     $this->assertNotEmpty($tracks);

    // }

    // public function testGetAlbumFromId()
    // {
    //     $albumId = '4aawyAB9vmqN3uQ7FjRGTy';
    //     $album = new Album($this->spotify, $albumId);
    //     $album->getData();
    //     $this->assertTrue($album->getName() === 'Global Warming');
    // }





    // public function testAccessTokenWithStoredData()
    // {
    //     $token = new AccessToken([
    //             "access_token" => "BQAhvMebwI2OcrlWDYp_tlYdcat4mqupBdUUx2YUppYt5N95CroMXwk7h0JwfVYxHTOznUSRivbOcDEmvu4oc7yIYfl9_YpjpFuAMaKhEF7SdpPS4gl6QRaTDnSY3Pebixe5Tr1sRE039Bj1W43G4e2UwmkKO5O4KfQtOJ7q3ykTGlBUtP9Owfxd_j-EZeRp4J1tj-6rIC5dx12c4xy4Ww",
    //             "token_type" => "Bearer",
    //             "expires_in" => 3600,
    //             "scope" => "user-read-email user-read-private",
    //             "refresh_token" => "AQA0X7Oa-FxKHPCi-GNlNWsJTj9zF00M3dJ9U1X1Iz--rR_y7c_v8O-NYQnjNPc2QmEbm-kPTUwAo7zOx47yHc6fkEhE7yd0g7SPZYL2aQsnZkmmw0WDrriPYfYqdF7lYts"
    //     ]);
    //     $client = new SpotifyClient($_ENV['SPOTIFY_CLIENT_ID'], $_ENV['SPOTIFY_CLIENT_SECRET']);
    //     $spotify = new Spotify($token, $client);
    //     $user = new User($spotify);
    //     $user->getData();
    //     // $this->assertArrayHasKey('userId', $user->getData());
    //     echo json_encode($user, JSON_PRETTY_PRINT);
    // }






}
