<?php declare(strict_types=1);
namespace App\Dto;

use DateTimeImmutable;
use InvalidArgumentException;

final class CreateTimeReportDTO {
    public function __construct(
        public readonly int $workplaceId,
        public readonly DateTimeImmutable $date,
        public readonly float $hours,
        public readonly ?string $info = null,
    ) {}

    public static function fromArray(array $data): self {
        $requiredFields = ['workplace_id', 'date', 'hours'];
        $missingFields = array_diff($requiredFields, array_keys($data));
        
        if (!empty($missingFields)) {
            throw new InvalidArgumentException(
                'Missing required fields: ' . implode(', ', $missingFields)
            );
        }

        try {
            return new self(
                (int) $data['workplace_id'],
                new DateTimeImmutable($data['date']),
                (float) $data['hours'],
                $data['info'] ?? null
            );
        } catch (\Exception $e) {
            throw new InvalidArgumentException('Invalid data format: ' . $e->getMessage());
        }
    }

    public function toArray(): array {
        return [
            'workplace_id' => $this->workplaceId,
            'date' => $this->date->format('Y-m-d'),
            'hours' => $this->hours,
            'info' => $this->info,
        ];
    }
}