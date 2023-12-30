<?php

namespace Aslamhus\SpotifyClient\Exception;

class SpotifyAccessExpiredException extends \Exception
{
    public function __construct($message, $code = 0, Exception $previous = null)
    {
        $message = "Spotify access token expired.";
        parent::__construct($message, $code, $previous);
    }
}
