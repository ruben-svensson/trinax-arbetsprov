<?php declare(strict_types=1);
namespace App\Api;

use Psr\Http\Client\ClientInterface;
use GuzzleHttp\Psr7\Request;
use DateTimeImmutable;
use App\Options\TimeReportFilterOptions;
use App\Dto\TimereportDTO;
use App\Dto\WorkplaceDTO;

class TrinaxApiClient {
    private ClientInterface $client;
    private string $apiKey;
    private string $baseUrl;

    public function __construct(ClientInterface $client, string $apiKey, string $baseUrl) {
        $this->client = $client;
        $this->apiKey = $apiKey;
        $this->baseUrl = $baseUrl;
    }

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
            $workplace = WorkplaceDTO::create(
                $workplaceData['id'],
                $workplaceData['name'],
                new DateTimeImmutable($workplaceData['created_time'])
            );

            $workplaces[] = $workplace;
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
                $queryParams['fromDate'] = $filter->fromDate->format('YYYY-MM-DD');
            }
            if ($filter->toDate !== null) {
                $queryParams['toDate'] = $filter->toDate->format('YYYY-MM-DD');
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
            $timeReports[] = TimereportDTO::create(
                $reportData['id'],
                $reportData['workplace_id'],
                new DateTimeImmutable($reportData['date']),
                floatval($reportData['hours']),
                $reportData['info'] ? : ''
            );
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

        return TimereportDTO::create(
            $data['id'],
            $data['workplace_id'],
            new DateTimeImmutable($data['date']),
            floatval($data['hours']),
            $data['info'] ? : ''
        );
    }
}