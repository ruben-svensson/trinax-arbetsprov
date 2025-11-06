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
}