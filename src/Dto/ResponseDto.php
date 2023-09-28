<?php

declare(strict_types=1);

namespace HealthChecksBundle\Dto;

use DateTime;

class ResponseDto
{
    public function __construct(
        private readonly string $name,
        private readonly string $status,
        private readonly string $description,
        private readonly float $duration
    )
    {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getDuration(): float
    {
        return $this->duration;
    }

    public function toArray(): array
    {
        return [
            $this->getName() => [
                'status' => $this->getStatus(),
                'description' => $this->getDescription(),
                'duration' => DateTime::createFromFormat('U.u', strval($this->getDuration() / 1000))->format("H:i:s.u"),
            ]
        ];
    }
}
