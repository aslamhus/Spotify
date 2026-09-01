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

class TestUser extends TestCase
{
    private Spotify $spotify;
    private User $user;

    public function __construct()
    {
        parent::__construct();
        $client = new SpotifyClient($_ENV['SPOTIFY_CLIENT_ID'], $_ENV['SPOTIFY_CLIENT_SECRET']);
        $token =   new AccessToken([
            "access_token" => $_ENV['ACCESS_TOKEN'],
            "token_type" => "Bearer",
            "expires_in" => 3600,
            "scope" => $_ENV['SCOPE'],
            "refresh_token" => $_ENV['REFRESH_TOKEN']
        ]);
        $this->spotify = new Spotify($token, $client);
    }


    public function testGetUserData()
    {
        try {
            $this->user = new User($this->spotify);
            $this->user->getData();
        } catch (\Exception $e) {
            if ($e->getCode() == 401) {
                $this->fail("Spotify token was invalid. Please check your .env file and add your token values. \n" . $e->getMessage());
            } else {
                $this->fail($e->getMessage());
            }
            return;
        }
        $this->assertNotEmpty($this->user);
        $this->assertEquals(User::class, $this->user::class);
    }
}
