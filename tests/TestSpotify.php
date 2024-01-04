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
            "access_token" => "BQCcroMuU4jLgxEEPqAwfdw36RuTi0aOm_t6Uhm_AW_F_csv7umypB4O2IWo9_Z5RTByMKP5UrDx-fWo_zQlp2FFOpCNqqSicIAIzZd5stqqg2gi7ED_JN6yHk7sgz1pqWzUVm3ISLg2jN6KeTyDF_cmegT3ycXzgokQgGmCAtb98xdmYZ_cBgKtGI7ywdtTa5PxKKymR5Ezc-fRLXL3zUVcBMZCJGBffi9wmT74ElT7TCrvUocROnYOL26jBbBpORekfpXSnqhWVi7EQbwI_6m9dwrf-Jto",
            "token_type" => "Bearer",
            "expires_in" => 3600,
            "scope" => "playlist-read-private playlist-read-collaborative ugc-image-upload playlist-modify-private playlist-modify-public user-read-email user-read-private",
            "refresh_token" => "AQB_Rvfmm-ahUwh8WLkucD3bRxMGdgYMBNQc46fRaB6ik9qKOBqC10ByzZ2a8nKlAxNr2R2sRNORVXVmUbYe4HLG5qzo5HeSEY7eyCyALkfejCeIa7Q3q3ZmpJ4jRpsOLh0"

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

    public function testSearchArtist()
    {
        $artistData = $this->spotify->search('Steely Dan', 'artist');
        var_dump($artistData);
        $this->assertNotEmpty($artistData);
    }

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
