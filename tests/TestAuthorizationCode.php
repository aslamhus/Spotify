<?php


require_once __DIR__ . '/config.php';

use PHPUnit\Framework\TestCase;

use Aslamhus\SpotifyClient\Auth\AuthCodeToken;
use Aslamhus\SpotifyClient\Auth\ClientCredentials;
use Aslamhus\SpotifyClient\Auth\AuthorizationCode;
use Aslamhus\SpotifyClient\SpotifyClient;
use Aslamhus\SpotifyClient\Spotify;
use Aslamhus\SpotifyClient\Auth\AccessToken;
use Aslamhus\SpotifyClient\Interfaces\AuthorizationInterface;

define('REDIRECT_URI', 'http://localhost:3003/logsheet-reader/backend/authorize.php');
class TestAuthorizationCode extends TestCase
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

    }

    // public function testGetAuthorizeUrl()
    // {

    //     $scopes = [
    //         'user-read-private',
    //         'user-read-email',
    //         'playlist-read-private',
    //         'playlist-read-collaborative',
    //         'playlist-modify-private',
    //         'playlist-modify-public',
    //         'ugc-image-upload'
    //     ];
    //     $url = AuthorizationCode::getAuthorizeUrl($_ENV['SPOTIFY_CLIENT_ID'], REDIRECT_URI, implode(' ', $scopes));
    //     $this->assertNotEmpty($url);
    //     echo $url;
    // }

    public function testGetUserAccessToken()
    {
        $code = 'AQB55T-9fWTpdA9Qu4o-cCoR8BXu0XvLL_U7F6cRbqwbXbPKZB_HWX9IZ9RRUkXOc5N4GIuPXKkYJjbjhjGfikM5bqBAUJ0-ejSNgn5bjOTehMlnamOjolGju7kewM90IKol_YscBvnoKXAXrV2jCBC5q7xin6Uc7nqetCVpk6JXZMO9WU2GVj3LYngAfdwU9SSIpfatUU3DTGILW8nvlmwQTDQFgx1wPtMnZrBA6dXSAM2b_NwIIlyxbynXntR-oEXjsrft5MVew3kAzTNJNnsKEY6cuNThx2E2v8dbiFQlMR3JVJab65mdmXf86su4-__Kz7j-O2HI2cKZgvZ7MCrOWuVMbF7BdPfJmKSb4n_SEl6APssBEXoPMXVxKFyMDdHpSjFoE9skMG9tU2wXXizbEjDbqCW4ZZs';
        $token = new AuthorizationCode($this->client, $code, REDIRECT_URI);
        echo json_encode($token, JSON_PRETTY_PRINT);
        $this->assertNotEmpty($token->getToken()->getAccessToken());
    }






}
