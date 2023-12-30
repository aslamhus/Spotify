<?php

namespace Aslamhus\SpotifyClient\Interfaces;

interface AccessTokenInterface
{
    public function getAccessToken(): string;
    public function getTokenType(): string;
    public function getExpiresIn(): int;
    public function getScope(): string;
}
