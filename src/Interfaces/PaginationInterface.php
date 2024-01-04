<?php

namespace Aslamhus\SpotifyClient\Interfaces;

interface PaginationInterface
{
    public function fetchNext(): array;
    public function fetchPrevious(): array;
}
