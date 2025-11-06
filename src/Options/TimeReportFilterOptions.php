<?php declare(strict_types=1);
namespace App\Options;

use DateTimeImmutable;

final class TimeReportFilterOptions {
    public function __construct(
        public readonly ?int $workplaceId = null,
        public readonly ?DateTimeImmutable $fromDate = null,
        public readonly ?DateTimeImmutable $toDate = null
    ) {}

    public function toQueryParams(): array {
        return array_filter([
            'workplaceId' => $this->workplaceId,
            'fromDate'    => $this->fromDate ? $this->fromDate->format('Y-m-d') : null,
            'toDate'      => $this->toDate ? $this->toDate->format('Y-m-d') : null,
        ]);
    }
}