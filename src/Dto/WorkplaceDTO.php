<?php
namespace App\Dto;

use DateTimeImmutable;

final class WorkplaceDTO {
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly DateTimeImmutable $createdTime
    ) {}

    public static function create(int $id, string $name, DateTimeImmutable $createdTime): WorkplaceDTO {
        return new self($id, $name, $createdTime);
    }
}