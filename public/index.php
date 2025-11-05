<?php
require __DIR__ . '/../vendor/autoload.php';

use App\Api\TrinaxApiClient;

if (file_exists(__DIR__ . '/../.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
    $dotenv->load();
}

$apiKey = getenv('TRINAX_API_KEY');
$baseUrl = getenv('TRINAX_BASE_URL');

if (!$apiKey) {
    throw new RuntimeException('TRINAX_API_KEY environment variable is not set');
}

$client = new TrinaxApiClient($apiKey, $baseUrl);
$workplaces = $client->getWorkplaces();

var_dump($workplaces);