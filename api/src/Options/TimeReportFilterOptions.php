<?php declare(strict_types=1);
namespace App\Options;

use DateTimeImmutable;

final class TimeReportFilterOptions {
    public function __construct(
        public readonly ?int $workplace = null,
        public readonly ?DateTimeImmutable $fromDate = null,
        public readonly ?DateTimeImmutable $toDate = null
    ) {}

    /**
     * Create from HTTP query parameters
     */
    public static function fromQueryParams(array $queryParams): self {
        return new self(
            workplace: isset($queryParams['workplace']) ? (int)$queryParams['workplace'] : null,
            fromDate: isset($queryParams['from_date']) ? new DateTimeImmutable($queryParams['from_date']) : null,
            toDate: isset($queryParams['to_date']) ? new DateTimeImmutable($queryParams['to_date']) : null,
        );
    }

    /**
     * Convert to query parameters for external API
     */
    public function toQueryParams(): array {
        return array_filter([
            'workplace' => $this->workplace,
            'from_date' => $this->fromDate?->format('Y-m-d'),
            'to_date'   => $this->toDate?->format('Y-m-d'),
        ]);
    }
}