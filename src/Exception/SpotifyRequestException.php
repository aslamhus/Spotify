<?php

namespace Aslamhus\SpotifyClient\Exception;

class SpotifyRequestException extends \Exception
{
    public int $statusCode = 0;
    public array $body = [];

    public function __construct($message, \Psr\Http\Message\ResponseInterface $response = null)
    {
        // if response set, parse status code, body, error, and error description
        if($response) {
            $this->statusCode = $response->getStatusCode();
            $this->body = json_decode($response->getBody(), true);
        }

        $message = "Spotify request error: " . $message;
        // call parent constructor
        parent::__construct($message, 0, null);

    }

    /**
     * Get the error object
     */
    public function getBody(): array
    {
        return $this->body;
    }


    /**
     * Get the status code
     *
     * @return integer
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }



}
