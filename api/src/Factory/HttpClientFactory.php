<?php declare(strict_types=1);
namespace App\Factory;

use App\Mock\DatabaseMockClient;
use GuzzleHttp\Client as GuzzleClient;
use PDO;
use Psr\Http\Client\ClientInterface;

class HttpClientFactory {
    public static function create(string $environment, PDO $pdo): ClientInterface {
        return match ($environment) {
            'development' => new DatabaseMockClient($pdo),
            'production' => new GuzzleClient(),
            default => throw new \RuntimeException('Unknown or unset APP_ENV environment variable'),
        };
    }
}