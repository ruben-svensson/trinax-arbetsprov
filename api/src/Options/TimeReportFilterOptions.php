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
            'workplace_id' => $this->workplaceId,
            'from_date'    => $this->fromDate ? $this->fromDate->format('Y-m-d') : null,
            'to_date'      => $this->toDate ? $this->toDate->format('Y-m-d') : null,
        ]);
    }

    public static function fromArray(string $query): self {
        parse_str($query, $params);

        return new self(
            workplaceId: isset($params['workplace_id']) ? (int)$params['workplace_id'] : null,
            fromDate: isset($params['from_date']) ? new DateTimeImmutable($params['from_date']) : null,
            toDate: isset($params['to_date']) ? new DateTimeImmutable($params['to_date']) : null,
        );
    }
}