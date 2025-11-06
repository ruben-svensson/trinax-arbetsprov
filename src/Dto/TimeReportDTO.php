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

    public static function create(int $id, int $workplace_id, DateTimeImmutable $date, float $hours, ?string $info): TimeReportDTO {
        return new self($id, $workplace_id, $date, $hours, $info);
    }
}