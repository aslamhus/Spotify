<?php


require_once __DIR__ . '/../../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../..');
$dotenv->load();
$dotenv->required(['SPOTIFY_CLIENT_ID'])->notEmpty();
$dotenv->required(['SPOTIFY_CLIENT_SECRET'])->notEmpty();
