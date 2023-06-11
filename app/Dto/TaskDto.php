<?php

namespace App\Dto;

readonly class TaskDto
{
    public bool $completed;

    public function __construct(
        public string $title,
        public string $description,
        public ?string $long_description = null,
        ?string $completed = '',
    )
    {
        $this->completed = (bool)$completed;
    }
}
