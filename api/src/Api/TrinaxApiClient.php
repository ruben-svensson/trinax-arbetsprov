<?php declare(strict_types=1);
namespace App\Api;

use App\Dto\CreateTimeReportDTO;
use Psr\Http\Client\ClientInterface;
use GuzzleHttp\Psr7\Request;
use App\Options\TimeReportFilterOptions;
use App\Dto\TimeReportDTO;
use App\Dto\WorkplaceDTO;
use DI\Attribute\Inject;

class TrinaxApiClient {

    public function __construct(
        private ClientInterface $client,
        #[Inject('api.key')] private string $apiKey,
        #[Inject('api.baseUrl')] private string $baseUrl
    ) {}

    /**
     * @return WorkplaceDTO[]
     */
    public function getWorkplaces(): array {
        $data = $this->sendRequest('GET', 'workplace');
        return array_map(fn($item) => WorkplaceDTO::fromArray($item), $data);
    }

    /**
     * @return TimeReportDTO[]
     */
    public function getTimeReports(?TimeReportFilterOptions $filter = null): array {
        $queryParams = $filter?->toQueryParams() ?? [];
        $data = $this->sendRequest('GET', 'timereport', $queryParams);

        return array_map(fn($item) => TimereportDTO::fromArray($item), $data);
    }

    public function getTimeReport(int $id): ?TimeReportDTO {
        $data = $this->sendRequest('GET', 'timereport/' . $id);

        return TimereportDTO::fromArray($data);
    }

    public function createTimeReport(CreateTimeReportDTO $timeReport): TimeReportDTO {
        $data = $this->sendRequest('POST', 'timereport', [], $timeReport->toArray());

        return TimeReportDTO::fromArray($data);
    }

    private function sendRequest(string $method, string $endpoint, array $queryParams = [], ?array $body = null): array {
        $url = $this->baseUrl . $endpoint;
        if (!empty($queryParams)) {
            $url .= '?' . http_build_query($queryParams);
        }

        $headers =[
            'Authorization' => 'bearer ' . $this->apiKey,
            'Accept'        => 'application/json',
        ];

        $requestBody = null;
        if ($body !== null) {
            $headers['Content-Type'] = 'application/json';
            $requestBody = json_encode($body);
        }

        $req = new Request($method, $url, $headers, $requestBody);

        $res = $this->client->sendRequest($req);
        $statusCode = $res->getStatusCode();

        return json_decode($res->getBody()->getContents(), true);
    }
}