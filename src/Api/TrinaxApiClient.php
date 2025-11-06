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
        $req = new Request('GET', $this->baseUrl . 'workplace', [
            'Authorization' => 'bearer ' . $this->apiKey,
            'Accept'        => 'application/json',
        ]);

        $res = $this->client->sendRequest($req);
        $data = json_decode($res->getBody()->getContents(), true);

        $workplaces = [];
        foreach ($data as $workplaceData){
            $workplaces[] = WorkplaceDTO::fromArray($workplaceData);
        }

        return $workplaces;
    }

    /**
     * @return TimeReportDTO[]
     */
    public function getTimeReports(?TimeReportFilterOptions $filter = null): array {
        $queryParams = [];

        if ($filter) {
            if ($filter->workplaceId !== null) {
                $queryParams['workplaceId'] = $filter->workplaceId;
            }
            if ($filter->fromDate !== null) {
                $queryParams['fromDate'] = $filter->fromDate->format('Y-m-d');
            }
            if ($filter->toDate !== null) {
                $queryParams['toDate'] = $filter->toDate->format('Y-m-d');
            }
        }

        
        $url = $this->baseUrl . 'timereport';
        if (!empty($queryParams)) {
            $url .= '?' . http_build_query($queryParams);
        }

        $req = new Request('GET', $url, [
            'Authorization' => 'bearer ' . $this->apiKey,
            'Accept'        => 'application/json',
        ]);

        $res = $this->client->sendRequest($req);
        $data = json_decode($res->getBody()->getContents(), true);

        $timeReports = [];
        foreach ($data as $reportData) {
            $timeReports[] = TimereportDTO::fromArray($reportData);
        }

        return $timeReports;
    }

    public function getTimeReport(int $id): ?TimeReportDTO {
        $req = new Request('GET', $this->baseUrl . 'timereport/' . $id, [
            'Authorization' => 'bearer ' . $this->apiKey,
            'Accept'        => 'application/json',
        ]);

        $res = $this->client->sendRequest($req);
        if ($res->getStatusCode() === 404) {
            return null;
        }

        $data = json_decode($res->getBody()->getContents(), true);

        return TimereportDTO::fromArray($data);
    }
}