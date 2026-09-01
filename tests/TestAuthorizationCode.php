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

    public function testGetAuthorizeUrl()
    {
        $url = AuthorizationCode::getAuthorizeUrl($_ENV['SPOTIFY_CLIENT_ID'], $_ENV['REDIRECT_URI'], $_ENV['SCOPE']);
        $this->assertNotEmpty($url);
    }

    /**
     * Use testGetAuthorizeUrl to get the authorization code,
     * then enter it in the function below to get the access / refresh token
     * @return void 
     */
    public function testGetUserAccessToken()
    {
        return;
        $code = '<ENTER CODE FROM AUTHORIZATION>';
        $token = new AuthorizationCode($this->client, $code, $_ENV['REDIRECT_URI']);
        echo json_encode($token, JSON_PRETTY_PRINT);
        $this->assertNotEmpty($token->getToken()->getAccessToken());
    }
}
