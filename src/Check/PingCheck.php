<?php

declare(strict_types=1);

namespace SymfonyHealthCheckBundle\Check;

use GuzzleHttp\Client;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Stopwatch\Stopwatch;
use SymfonyHealthCheckBundle\Dto\ResponseDto;
use SymfonyHealthCheckBundle\Enum\Status;
use Throwable;

class PingCheck implements CheckInterface
{
    public const RESPONSE_TEXT = 'pong';

    public function __construct(
        private readonly string $name,
        private readonly string $endpoint
    )
    {
    }

    public function check(): ResponseDto
    {
        $stopwatch = new Stopwatch(true);

        $stopwatch->start('status_up');

        $client = new Client();
        try {
            $response = $client->request('GET', $this->endpoint);
            $statusCode = $response->getStatusCode();
            $responseText = $response->getBody();
        } catch (Throwable $e) {
            $statusCode = 0;
            $responseText = '';
        }

        $event = $stopwatch->stop('status_up');

        $responseStatus = match (true) {
            $statusCode !== Response::HTTP_OK => Status::FAIL,
            $responseText !== self::RESPONSE_TEXT => Status::WARNING,
            default => Status::PASS,
        };

        return new ResponseDto(
            $this->name,
            $responseStatus,
            'Description',
            $event->getDuration()
        );
    }
}
