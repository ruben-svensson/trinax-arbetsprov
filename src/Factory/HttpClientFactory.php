<?php declare(strict_types=1);
namespace App\Factory;

use App\Dto\WorkplaceDTO;
use DateTimeImmutable;
use Http\Mock\Client as MockClient;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Psr7\Response;
use Http\Message\RequestMatcher\RequestMatcher;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;

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

        $workplaces = [
            [
                'id' => 1,
                'name' => 'Workplace A',
                'created_time' => '2023-01-01T12:00:00+00:00', // Use ISO 8601 string format
            ],
            [
                'id' => 2,
                'name' => 'Workplace B',
                'created_time' => '2023-02-01T12:00:00+00:00',
            ],
        ];
        $timeReports = [
            [
                'id' => 1,
                'workplace_id' => 1,
                'date' => '2023-03-01T00:00:00+00:00',
                'hours' => 8.0,
                'info' => 'Worked on project X',
            ],
            [
                'id' => 2,
                'workplace_id' => 2,
                'date' => '2023-03-02T00:00:00+00:00',
                'hours' => 6.5,
                'info' => 'Meeting and documentation',
            ],
        ];

        $workplaceGetMatcher = new RequestMatcher('/workplace', null, 'GET');
        $mockClient->on($workplaceGetMatcher, function (RequestInterface $request) use ($workplaces) {

            return new Response(
                200,
                ['Content-Type' => 'application/json'],
                json_encode($workplaces)
            );
        });

        $timeReportGetMatcher = new RequestMatcher('/timereport', null, 'GET');
        $mockClient->on($timeReportGetMatcher, function (RequestInterface $request) use ($timeReports) {

            return new Response(
                200,
                ['Content-Type' => 'application/json'],
                json_encode($timeReports)
            );
        });

        $timeReportPostMatcher = new RequestMatcher('/timereport', null, 'POST');
        $mockClient->on($timeReportPostMatcher, function (RequestInterface $request) use (&$timeReports) {
            $body = json_decode($request->getBody()->getContents(), true);
            
            $randId = count($timeReports) + 1;
            $body['id'] = $randId;

            return new Response(
                201,
                ['Content-Type' => 'application/json'],
                json_encode($body)
            );
        });

        return $mockClient;
    }

    private static function createProductionClient(): ClientInterface {
        return new GuzzleClient();
    }
}