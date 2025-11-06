<?php declare(strict_types=1);
require __DIR__ . '/../vendor/autoload.php';

use App\Api\TrinaxApiClient;
use App\Factory\HttpClientFactory;
use DI\Container;
use DI\ContainerBuilder;
use Psr\Http\Client\ClientInterface;

use function DI\env;

if (file_exists(__DIR__ . '/../.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
    $dotenv->load();
}
/*
$appEnv = getenv('APP_ENV');
$apiKey = getenv('TRINAX_API_KEY');
$baseUrl = getenv('TRINAX_BASE_URL');

if (!$apiKey) {
    throw new RuntimeException('TRINAX_API_KEY environment variable is not set');
}

$httpClient = HttpClientFactory::create($appEnv);

$client = new TrinaxApiClient($httpClient, $apiKey, $baseUrl);
$workplaces = $client->getWorkplaces();
$timeReports = $client->getTimeReports();*/

$containerBuilder = new ContainerBuilder();
$containerBuilder->useAttributes(true);
$containerBuilder->addDefinitions([
    'app.env' => env('APP_ENV', ''),
    'api.key' => env('TRINAX_API_KEY', ''),
    'api.baseUrl' => env('TRINAX_BASE_URL', ''),

    ClientInterface::class => DI\factory(function (Container $c) {
        return HttpClientFactory::create($c->get('app.env'));
    }),

    TrinaxApiClient::class => Di\autowire()
]);

$container = $containerBuilder->build();

$client = $container->get(App\Api\TrinaxApiClient::class);
$workplaces = $client->getWorkplaces();

var_dump($workplaces);