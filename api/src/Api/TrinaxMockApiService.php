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
        $sql = 'SELECT 
                    tr.id, 
                    tr.workplace_id, 
                    tr.date, 
                    tr.hours, 
                    tr.info,
                    img.filename
                FROM mock_timereports tr
                LEFT JOIN mock_timereport_images img ON tr.id = img.timereport_id
                WHERE 1=1';
        $bindings = [];

        if ($filter?->workplaceId !== null) {
            $sql .= ' AND tr.workplace_id = ?';
            $bindings[] = $filter->workplaceId;
        }

        if ($filter?->fromDate !== null) {
            $sql .= ' AND tr.date >= ?';
            $bindings[] = $filter->fromDate->format('Y-m-d');
        }

        if ($filter?->toDate !== null) {
            $sql .= ' AND tr.date <= ?';
            $bindings[] = $filter->toDate->format('Y-m-d');
        }

        $sql .= ' ORDER BY tr.date DESC';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($bindings);
        $rows = $stmt->fetchAll();

        return array_map(function($row) {
            // Add image_url if an image exists
            if ($row['filename'] !== null) {
                $row['image_url'] = "/api/timereport/{$row['id']}/image";
            }
            return TimeReportDTO::fromArray($row);
        }, $rows);
    }

    public function getTimeReport(int $id): ?TimeReportDTO
    {
        $stmt = $this->pdo->prepare('
            SELECT 
                tr.id, 
                tr.workplace_id, 
                tr.date, 
                tr.hours, 
                tr.info,
                img.filename
            FROM mock_timereports tr
            LEFT JOIN mock_timereport_images img ON tr.id = img.timereport_id
            WHERE tr.id = ?
        ');
        $stmt->execute([$id]);
        $row = $stmt->fetch();

        if (!$row) {
            return null;
        }

        // Add image_url if an image exists
        if ($row['filename'] !== null) {
            $row['image_url'] = "/api/timereport/{$row['id']}/image";
        }

        return TimeReportDTO::fromArray($row);
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