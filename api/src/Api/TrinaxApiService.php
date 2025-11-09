<?php declare(strict_types=1);
namespace App\Api;

use App\Database;
use App\Dto\CreateTimeReportDTO;
use Psr\Http\Client\ClientInterface;
use GuzzleHttp\Psr7\Request;
use App\Options\TimeReportFilterOptions;
use App\Dto\TimeReportDTO;
use App\Dto\WorkplaceDTO;
use DI\Attribute\Inject;

class TrinaxApiService implements TrinaxApiServiceInterface {

    public function __construct(
        private ClientInterface $client,
        #[Inject('api.key')] private string $apiKey,
        #[Inject('api.baseUrl')] private string $baseUrl,
        private Database $database // Inject the database to check for images
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

        return array_map(function($item) {
            $filename = $this->database->getImageForReport($item['id']);
            if ($filename !== null) {
                $item['image_url'] = "/api/timereport/{$item['id']}/image";
            }
            return TimereportDTO::fromArray($item);
        }, $data);
    }

    public function getTimeReport(int $id): ?TimeReportDTO {
        $data = $this->sendRequest('GET', 'timereport/' . $id);
        
        // Check if this report has an image in our local database
        $filename = $this->database->getImageForReport($id);
        if ($filename !== null) {
            $data['image_url'] = "/api/timereport/{$id}/image";
        }

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

        $headers = [
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

        return json_decode($res->getBody()->getContents(), true);
    }
}