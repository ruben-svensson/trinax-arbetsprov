<?php declare(strict_types=1);
namespace App\Action;

use App\Api\TrinaxApiServiceInterface;
use App\Options\TimeReportFilterOptions;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ListTimeReportAction extends Action {

    public function __construct(
        private TrinaxApiServiceInterface $service,
    )
    {}

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args) {
        if (isset($args['id'])) {
            return $this->getSingle($response, (int) $args['id']);
        }

        return $this->getAll($request, $response);
    }

    private function getAll(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface {
        try {
            $queryParams = $request->getQueryParams();
            
            $filters = new TimeReportFilterOptions(
                workplaceId: isset($queryParams['workplaceId']) ? (int)$queryParams['workplaceId'] : null,
                fromDate: isset($queryParams['from_date']) ? new \DateTimeImmutable($queryParams['from_date']) : null,
                toDate: isset($queryParams['to_date']) ? new \DateTimeImmutable($queryParams['to_date']) : null,
            );

            $timeReports = $this->service->getTimeReports($filters);
            return $this->jsonResponse($response, $timeReports);
        } catch (Exception $e) {
            return $this->jsonResponse($response, ['error' => 'Invalid query parameters'], 400);
        }
    }

    private function getSingle(ResponseInterface $response, int $id): ResponseInterface {
        if ($id <= 0) {
            return $this->jsonResponse($response, ['error' => 'Invalid time report ID'], 400);
        }

        try {
            $timeReport = $this->service->getTimeReport($id);
            
            if ($timeReport === null) {
                return $this->jsonResponse($response, ['error' => 'Time report not found'], 404);
            }

            return $this->jsonResponse($response, $timeReport);
            
        } catch (Exception $e) {
            return $this->jsonResponse($response, ['error' => 'Failed to fetch time report'], 500);
        }
    }
}