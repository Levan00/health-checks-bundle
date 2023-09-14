<?php

declare(strict_types=1);

namespace SymfonyHealthCheckBundle\Dto;

class Response
{
    public function __construct(
        private readonly string $component,
        private readonly string $status,
        private readonly string $description,
        private readonly string $duration
    )
    {
    }

    public function getComponent(): string
    {
        return $this->component;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getDuration(): string
    {
        return $this->duration;
    }

    public function toArray(): array
    {
        return [
            $this->getComponent() => [
                'status' => $this->getStatus(),
                'description' => $this->getDescription(),
                'duration' => $this->getDuration(),
            ]
        ];
    }
}
