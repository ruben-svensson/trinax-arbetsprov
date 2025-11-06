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

$requiredEnvVars = ['APP_ENV', 'TRINAX_API_KEY', 'TRINAX_BASE_URL'];
foreach ($requiredEnvVars as $var) {
    if (empty(env($var))) {
        throw new InvalidArgumentException(sprintf('Environment variable %s is not set', $var));
    }
}

$containerBuilder = new ContainerBuilder();
$containerBuilder->useAttributes(true);
$containerBuilder->addDefinitions([
    'app.env' => env('APP_ENV'),
    'api.key' => env('TRINAX_API_KEY'),
    'api.baseUrl' => env('TRINAX_BASE_URL'),

    ClientInterface::class => DI\factory(function (Container $c) {
        return HttpClientFactory::create($c->get('app.env'));
    }),

    TrinaxApiClient::class => Di\autowire()
]);

$container = $containerBuilder->build();

$client = $container->get(App\Api\TrinaxApiClient::class);
$workplaces = $client->getWorkplaces();

var_dump($workplaces);