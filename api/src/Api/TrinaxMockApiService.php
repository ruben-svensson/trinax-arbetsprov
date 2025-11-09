<?php declare(strict_types=1);
namespace App\Api;

use App\Api\TrinaxApiServiceInterface;
use App\Dto\CreateTimeReportDTO;
use App\Dto\TimeReportDTO;
use App\Dto\WorkplaceDTO;
use App\Options\TimeReportFilterOptions;
use PDO;

class TrinaxMockApiService implements TrinaxApiServiceInterface {
    public function __construct(
        private PDO $pdo,
    ) {}

    public function getWorkplaces(): array
    {
        $stmt = $this->pdo->query('SELECT id, name, created_time FROM mock_workplaces ORDER BY id');
        $rows = $stmt->fetchAll();
        return array_map(fn($row) => WorkplaceDTO::fromArray($row), $rows);
    }

    public function getTimeReports(?TimeReportFilterOptions $filter = null): array
    {
        $sql = 'SELECT id, workplace_id, date, hours, info FROM mock_timereports WHERE 1=1';
        $bindings = [];

        if ($filter?->workplaceId !== null) {
            $sql .= ' AND workplace_id = ?';
            $bindings[] = $filter->workplaceId;
        }

        if ($filter?->fromDate !== null) {
            $sql .= ' AND date >= ?';
            $bindings[] = $filter->fromDate->format('Y-m-d');
        }

        if ($filter?->toDate !== null) {
            $sql .= ' AND date <= ?';
            $bindings[] = $filter->toDate->format('Y-m-d');
        }

        $sql .= ' ORDER BY date DESC';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($bindings);
        $rows = $stmt->fetchAll();

        return array_map(fn($row) => TimeReportDTO::fromArray($row), $rows);
    }

    public function getTimeReport(int $id): ?TimeReportDTO
    {
        $stmt = $this->pdo->prepare('SELECT id, workplace_id, date, hours, info FROM mock_timereports WHERE id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();

        return $row ? TimeReportDTO::fromArray($row) : null;
    }

    public function createTimeReport(CreateTimeReportDTO $timeReport): TimeReportDTO
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO mock_timereports (workplace_id, date, hours, info) VALUES (?, ?, ?, ?)'
        );
        $stmt->execute([
            $timeReport->workplaceId,
            $timeReport->date->format('Y-m-d'),
            $timeReport->hours,
            $timeReport->info
        ]);

        $id = (int)$this->pdo->lastInsertId();

        // Fetch and return the created record
        return $this->getTimeReport($id);
    }
}