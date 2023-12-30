<?php

namespace Aslamhus\SpotifyClient\Exception;

class AuthorizationException extends \Exception
{
    public int $statusCode;
    public array $error = [];

    public function __construct($message, \Psr\Http\Message\ResponseInterface $response)
    {

        // parse status code, body, error, and error description
        $this->statusCode = $response->getStatusCode();
        $this->error = json_decode($response->getBody(), true);
        $message = "Spotify authorization error: {$this->error['error']}, errorDescription: {$this->error['error_description']}.";
        // call parent constructor
        parent::__construct($message, 0, null);

    }

    /**
     * Get the error object
     *
     * @return array - ['error' => 'error', 'error_description' => 'error description']
     */
    public function getError(): array
    {
        return $this->error;
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
