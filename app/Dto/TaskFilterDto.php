<?php

declare(strict_types=1);

namespace App\Dto;

readonly class TaskFilterDto
{
    public function __construct(
        public ?bool $completed = null,
        /** Part of username for filtering task by user */
        public ?string $user = null,
    ) {
    }
}
