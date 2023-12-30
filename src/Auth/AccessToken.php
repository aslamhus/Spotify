<?php

namespace Aslamhus\SpotifyClient\Auth;

use Aslamhus\SpotifyClient\Interfaces\AccessTokenInterface;

class AccessToken implements \JsonSerializable, AccessTokenInterface
{
    private string $accessToken;
    private string $tokenType;
    private int $expiresIn;
    private string $scope;

    public function __construct($options = [])
    {
        $this->accessToken = $options['access_token'];
        $this->tokenType = $options['token_type'];
        $this->expiresIn = $options['expires_in'];
        // scope may be optional depending on the authorization flow
        $this->scope = $options['scope'] ?? '';

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


    public function jsonSerialize(): array
    {
        return [
            'access_token'  => $this->accessToken,
            'token_type'    => $this->tokenType,
            'expires_in'    => $this->expiresIn,
            'scope'         => $this->scope,
        ];
    }
}
