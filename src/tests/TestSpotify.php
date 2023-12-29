<?php


require_once __DIR__ . '/config.php';

use PHPUnit\Framework\TestCase;
use Aslamhus\SpotifyClient\Spotify;
use Aslamhus\SpotifyClient\SpotifyAccessToken;

class TestSpotify extends TestCase
{
    public function testGetAccessToken()
    {
        $spotifyAccessToken = new SpotifyAccessToken($_ENV['SPOTIFY_CLIENT_ID'], $_ENV['SPOTIFY_CLIENT_SECRET']);
        $accessToken = $spotifyAccessToken->getAccessToken();
        $this->assertNotEmpty($accessToken);
    }

    public function testGetArtistData()
    {
        $spotifyAccessToken = new SpotifyAccessToken($_ENV['SPOTIFY_CLIENT_ID'], $_ENV['SPOTIFY_CLIENT_SECRET']);
        $spotify = new Spotify($spotifyAccessToken);
        $artistData = $spotify->get('https://api.spotify.com/v1/artists/4Z8W4fKeB5YxbusRsdQVPb');
        var_dump($artistData);
        $this->assertNotEmpty($artistData);
    }

    public function testSearchArtist()
    {
        $spotifyAccessToken = new SpotifyAccessToken($_ENV['SPOTIFY_CLIENT_ID'], $_ENV['SPOTIFY_CLIENT_SECRET']);
        $spotify = new Spotify($spotifyAccessToken);
        $artistData = $spotify->search('Steely Dan', 'artist');
        $this->assertNotEmpty($artistData);
    }

}
