<?php

namespace Aslamhus\SpotifyClient\Interfaces;

use GuzzleHttp\Client;

interface AccessTokenInterface
{
    public function getAccessToken(): string;
    public function getTokenType(): string;
    public function getExpiresIn(): int;
    public function getScope(): string;
    public function getClient(): Client;
}
