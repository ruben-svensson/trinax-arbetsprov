<?php
namespace App\Factory;

use Http\Mock\Client as MockClient;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Client\ClientInterface;

class HttpClientFactory {
    public static function create(string $environment): ClientInterface {
        return match ($environment) {
            'development' => self::createMockClient(),
            'production' => self::createProductionClient(),
            default => throw new \RuntimeException('Unknown or unset APP_ENV environment variable'),
        };
    }

    private static function createMockClient(): ClientInterface {
        $mockClient = new MockClient();

        $fakeWorkplacesJson = json_encode([
            [
                'id' => 1,
                'name' => 'Workplace A',
                'createdTime' => '2023-01-01T12:00:00+00:00',
            ],
            [
                'id' => 2,
                'name' => 'Workplace B',
                'createdTime' => '2023-02-01T12:00:00+00:00',
            ],
        ]);

        $mockResponse = new Response(
            200,
            ['Content-Type' => 'application/json'],
            $fakeWorkplacesJson
        );

        $mockClient->addResponse($mockResponse);

        return $mockClient;
    }

    private static function createProductionClient(): ClientInterface {
        return new GuzzleClient();
    }
}