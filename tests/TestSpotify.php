<?php


require_once __DIR__ . '/config.php';

use PHPUnit\Framework\TestCase;

use Aslamhus\SpotifyClient\SpotifyAccessToken;
use Aslamhus\SpotifyClient\Spotify;
use Aslamhus\SpotifyClient\Artist\Artist;
use Aslamhus\SpotifyClient\Album\Album;
use Aslamhus\SpotifyClient\SpotifyUserAccessToken;
use Aslamhus\SpotifyClient\User\User;

define('REDIRECT_URI', 'http://localhost:3003/logsheet-reader/backend/authorize.php');
class TestSpotify extends TestCase
{
    private SpotifyAccessToken $token;
    private Spotify $spotify;

    public function __construct()
    {
        parent::__construct();
        $this->token = new SpotifyAccessToken($_ENV['SPOTIFY_CLIENT_ID'], $_ENV['SPOTIFY_CLIENT_SECRET']);
        $this->spotify = new Spotify($this->token);
    }

    public function testGetAccessToken()
    {
        $accessToken = $this->token->getAccessToken();
        $this->assertNotEmpty($accessToken);
    }

    public function testGetArtistData()
    {
        $artistData = $this->spotify->get('https://api.spotify.com/v1/artists/4Z8W4fKeB5YxbusRsdQVPb');
        $this->assertNotEmpty($artistData);
    }

    public function testSearchArtist()
    {
        $artistData = $this->spotify->search('Steely Dan', 'artist');
        $this->assertNotEmpty($artistData);
    }

    public function testGetArtistTracks()
    {

        $tracks = [];
        // search for an artist
        $artistName = 'Ed Cherry';
        $search = $this->spotify->search($artistName, 'artist');
        $items = $search['artists']['items'];
        $result = false;
        // find the artist that exactly matches the search term
        foreach ($items as $item) {
            if ($item['name'] == $artistName) {
                $result = $item;
                break;
            }
        }
        $this->assertArrayHasKey('id', $result);
        $artistId = $result['id'];
        // create Artist object
        $artists = new Artist($this->spotify, $artistId);
        // get all albums for artist
        $albums = $artists->getAlbums();
        // push all tracks for each album into $tracks array
        foreach($albums as $album) {
            $albumName = $album->getName();
            $this->assertNotEmpty($albumName);
            $albumTracks = $album->getTracks();
            foreach($albumTracks as $track) {
                $trackName = $track->getName();
                $this->assertNotEmpty($trackName);
                $tracks[$albumName][] = $trackName;
            }
        }
        echo json_encode($tracks, JSON_PRETTY_PRINT);
        $this->assertNotEmpty($tracks);

    }

    public function testGetAlbumFromId()
    {
        $albumId = '4aawyAB9vmqN3uQ7FjRGTy';
        $album = new Album($this->spotify, $albumId);
        $album->getData();
        $this->assertTrue($album->getName() === 'Global Warming');
    }

    public function testGetAuthorizeUrl()
    {

        $url = SpotifyUserAccessToken::getAuthorizeUrl($_ENV['SPOTIFY_CLIENT_ID'], REDIRECT_URI, 'user-read-private user-read-email');
        $this->assertNotEmpty($url);
    }



}
