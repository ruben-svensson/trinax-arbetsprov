<?php declare(strict_types=1);
namespace App\Dto;

use DateTimeImmutable;

final class TimeReportDTO {
    public function __construct(
        public readonly int $id,
        public readonly int $workplace_id,
        public readonly DateTimeImmutable $date,
        public readonly float $hours,
        public readonly ?string $info
    ) {}

    public static function fromArray(array $data): self {
        return new self(
            $data['id'],
            $data['workplace_id'],
            new DateTimeImmutable($data['date']),
            floatval($data['hours']),
            $data['info'] ?? null
        );
    }
}