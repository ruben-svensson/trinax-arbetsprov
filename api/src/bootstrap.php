<?php declare(strict_types=1);
require __DIR__ . '/../vendor/autoload.php';

use App\Api\TrinaxApiClient;
use App\Database;
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
    'app.env' => env('APP_ENV', ''),
    'api.key' => env('TRINAX_API_KEY'),
    'api.baseUrl' => env('TRINAX_BASE_URL'),

    PDO::class => function() {
        $host = 'db';
        $db   = 'trinax_db';
        $user = 'trinax_user';
        $pass = 'trinax_pass';
        $charset = 'utf8mb4';

        $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        return new PDO($dsn, $user, $pass, $options);
    },

    ClientInterface::class => DI\factory(function (Container $c) {
        return HttpClientFactory::create(
            $c->get('app.env'),
            $c->get(PDO::class) // Pass PDO to the factory
        );
    }),

    TrinaxApiClient::class => Di\autowire(),
    Database::class => Di\autowire(),
]);

return $containerBuilder->build();