<?php

declare(strict_types=1);

namespace App\Dto;

readonly class TaskDto
{
    public function __construct(
        public string $title,
        public string $description,
        public ?string $long_description = null,
        public bool $completed = false,
    ) {
    }
}
