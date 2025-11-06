<?php declare(strict_types=1);
namespace App\Options;

use DateTimeImmutable;

final class TimeReportFilterOptions {
    public function __construct(
        public readonly ?int $workplaceId = null,
        public readonly ?DateTimeImmutable $fromDate = null,
        public readonly ?DateTimeImmutable $toDate = null
    ) {}
}