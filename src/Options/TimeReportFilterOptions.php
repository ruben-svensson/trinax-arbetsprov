<?php declare(strict_types=1);
namespace App\Options;

use DateTimeImmutable;

final class TimeReportFilterOptions {
    public function __construct(
        public readonly ?int $workplaceId = null,
        public readonly ?DateTimeImmutable $fromDate = null,
        public readonly ?DateTimeImmutable $toDate = null
    ) {}

    public static function create(?int $workplaceId, ?DateTimeImmutable $fromDate, ?DateTimeImmutable $toDate): TimeReportFilterOptions {
        return new self($workplaceId, $fromDate, $toDate);
    }
}