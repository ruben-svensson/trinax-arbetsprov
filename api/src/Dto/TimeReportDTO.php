<?php declare(strict_types=1);
namespace App\Dto;

use DateTimeImmutable;

final class TimeReportDTO {
    public function __construct(
        public readonly int $id,
        public readonly int $workplace_id,
        public readonly DateTimeImmutable $date,
        public readonly float $hours,
        public readonly ?string $info,
        public readonly ?string $image_url = null
    ) {}

    public static function fromArray(array $data): self {
        return new self(
            (int) $data['id'],
            (int) $data['workplace_id'],
            new DateTimeImmutable($data['date']),
            (float) $data['hours'],
            $data['info'] ?? null,
            $data['image_url'] ?? null
        );
    }

    public function toArray(): array {
        return [
            'id' => $this->id,
            'workplace_id' => $this->workplace_id, // camelCase for frontend
            'date' => $this->date->format('Y-m-d'),
            'hours' => $this->hours,
            'info' => $this->info,
            'image_url' => $this->image_url,
        ];
    }
}