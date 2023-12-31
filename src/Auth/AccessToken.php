<?php

namespace Aslamhus\SpotifyClient\Auth;

use Aslamhus\SpotifyClient\Interfaces\AccessTokenInterface;

class AccessToken implements \JsonSerializable, AccessTokenInterface
{
    private string $accessToken;
    private string $tokenType;
    private int $expiresIn;
    private string $scope;
    private string $refreshToken;

    public function __construct($options = [])
    {
        $this->accessToken = $options['access_token'];
        $this->tokenType = $options['token_type'];
        $this->expiresIn = $options['expires_in'];
        // optional fields depending on the authorization flow
        $this->scope = $options['scope'] ?? '';
        $this->refreshToken = $options['refresh_token'] ?? '';


    }


    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    public function getTokenType(): string
    {
        return $this->tokenType;
    }

    public function getExpiresIn(): int
    {
        return $this->expiresIn;
    }

    public function getScope(): string
    {
        return $this->scope;
    }

    public function getRefreshToken(): string
    {
        return $this->refreshToken;
    }


    public function jsonSerialize(): array
    {
        return [
            'access_token'  => $this->accessToken,
            'token_type'    => $this->tokenType,
            'expires_in'    => $this->expiresIn,
            'scope'         => $this->scope,
            'refresh_token' => $this->refreshToken,
        ];
    }
}
