<?php declare(strict_types=1);
namespace App\Dto;

use DateTimeImmutable;
use InvalidArgumentException;

final class CreateTimeReportDTO {
    public function __construct(
        public readonly int $workplaceId,
        public readonly DateTimeImmutable $date,
        public readonly float $hoursWorked,
        public readonly ?string $info = null,
    ) {}

    public static function fromRequest(array $postData): self {
        if (empty($postData['date']) || empty($postData['hours']) || empty($postData['workplace_id'])) {
            throw new InvalidArgumentException('Missing required fields: date, hours, workplace_id, got: ' . implode(', ', array_keys($postData)));
        }

        return new self(
            (int) $postData['workplace_id'],
            new DateTimeImmutable($postData['date']),
            (float) $postData['hours'],
            $postData['info'] ?? null
        );
    }

    public function toArray(): array {
        return [
            'workplace_id' => $this->workplaceId,
            'date' => $this->date->format('Y-m-d'),
            'hours' => $this->hoursWorked,
            'info' => $this->info,
        ];
    }
}