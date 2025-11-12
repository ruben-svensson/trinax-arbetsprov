<?php declare(strict_types=1);
namespace App\Api;

use App\Dto\CreateTimeReportDTO;
use App\Dto\TimeReportDTO;
use App\Dto\WorkplaceDTO;
use App\Options\TimeReportFilterOptions;

interface TrinaxApiServiceInterface {
    /**
     * @return WorkplaceDTO[]
     */
    public function getWorkplaces(): array;

    /**
     * @return TimeReportDTO[]
     */
    public function getTimeReports(?TimeReportFilterOptions $filter = null): array;

    public function getTimeReport(int $id): ?TimeReportDTO;

    public function createTimeReport(CreateTimeReportDTO $timeReport): TimeReportDTO;
}
