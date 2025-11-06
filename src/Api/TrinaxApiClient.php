<?php declare(strict_types=1);
namespace App\Api;

use Psr\Http\Client\ClientInterface;
use GuzzleHttp\Psr7\Request;
use DateTimeImmutable;
use App\Options\TimeReportFilterOptions;
use App\Dto\TimereportDTO;
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

    private function sendRequest(string $method, string $endpoint, array $queryParams = []): array {
        $url = $this->baseUrl . $endpoint;
        if (!empty($queryParams)) {
            $url .= '?' . http_build_query($queryParams);
        }

        $req = new Request($method, $url, [
            'Authorization' => 'bearer ' . $this->apiKey,
            'Accept'        => 'application/json',
        ]);

        $res = $this->client->sendRequest($req);
        return json_decode($res->getBody()->getContents(), true);
    }
}