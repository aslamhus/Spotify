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
use Aslamhus\SpotifyClient\Track\Track;

class TestTrack extends TestCase
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


    public function testArtistEntityInTrackData()
    {
        $track = new Track($this->spotify, '1DMPTsIP3eHHu9cRtthuDe');
        $track->getData();
        $artists = $track->getArtists();
        $this->assertTrue(is_array($artists));
        $this->assertTrue(!empty($artists[0]->getName()));
    }




}
