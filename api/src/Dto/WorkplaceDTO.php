<?php declare(strict_types=1);
namespace App\Dto;

use DateTimeImmutable;

final class WorkplaceDTO {
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly DateTimeImmutable $createdTime
    ) {}

    public static function fromArray(array $data): self {
        return new self(
            $data['id'],
            $data['name'],
            new DateTimeImmutable($data['created_time'])
        );
    }

    public function toArray(): array {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'created_time' => $this->createdTime->format('Y-m-d H:i:s'),
        ];
    }
}