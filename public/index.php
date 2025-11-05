<?php
require __DIR__ . '/../vendor/autoload.php';

use App\Api\TrinaxApiClient;
use App\Factory\HttpClientFactory;

if (file_exists(__DIR__ . '/../.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
    $dotenv->load();
}

$httpClient = null;

$appEnv = getenv('APP_ENV');
$apiKey = getenv('TRINAX_API_KEY');
$baseUrl = getenv('TRINAX_BASE_URL');

if (!$apiKey) {
    throw new RuntimeException('TRINAX_API_KEY environment variable is not set');
}

$httpClient = HttpClientFactory::create($appEnv);

$client = new TrinaxApiClient($httpClient, $apiKey, $baseUrl);
$workplaces = $client->getWorkplaces();

var_dump($workplaces);