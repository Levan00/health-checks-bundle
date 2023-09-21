<?php

declare(strict_types=1);

namespace SymfonyHealthCheckBundle\Check\Ping;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Stopwatch\Stopwatch;
use Symfony\Component\HttpClient\HttpClient;
use SymfonyHealthCheckBundle\Check\CheckInterface;
use SymfonyHealthCheckBundle\Dto\ResponseDto;
use SymfonyHealthCheckBundle\Enum\Status;
use Throwable;

class StatusUpCheck implements CheckInterface
{
    public const RESPONSE_TEXT = 'pong';

    public function check(): ResponseDto
    {
        $stopwatch = new Stopwatch(true);

        $stopwatch->start('status_up');

        $client = HttpClient::create(['http_version' => '2.0']);
        try {
            $response = $client->request('GET', 'https://local_geoip.ringostat.net/ping');
            $statusCode = $response->getStatusCode();
            $responseText = $response->getContent();
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
            'Status up name',
            $responseStatus,
            'Status up description',
            $event->getDuration()
        );
    }
}
