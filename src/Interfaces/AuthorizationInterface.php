<?php

namespace Aslamhus\SpotifyClient\Interfaces;

use Aslamhus\SpotifyClient\Auth\AccessToken;
use GuzzleHttp\Client;

interface AuthorizationInterface
{
    public function getToken(): AccessToken;
}
