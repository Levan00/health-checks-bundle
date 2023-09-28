<?php

declare(strict_types=1);

namespace HealthChecksBundle\Check;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Stopwatch\Stopwatch;
use HealthChecksBundle\Dto\ResponseDto;
use HealthChecksBundle\Enum\Status;
use Throwable;

class PingCheck implements CheckInterface
{
    private const RESPONSE_TEXT = 'pong';
    private const CHECK_NAME_PATTERN = '%s:ping';
    private const CHECK_DESCRIPTION_PATTERN = 'Ping \'%s\' endpoint: %s';

    public function __construct(
        private readonly string $service,
        private readonly string $endpoint
    )
    {
    }

    public function check(): ResponseDto
    {
        $stopwatch = new Stopwatch(true);

        $stopwatch->start('ping');

        $client = HttpClient::create();
        try {
            $response = $client->request('GET', $this->endpoint);
            $statusCode = $response->getStatusCode();
            $responseText = $response->getContent();
        } catch (Throwable $e) {
            $statusCode = 0;
            $responseText = '';
        }

        $responseStatus = match (true) {
            $statusCode !== Response::HTTP_OK => Status::FAIL,
            $responseText !== self::RESPONSE_TEXT => Status::WARNING,
            default => Status::PASS,
        };

        return new ResponseDto(
            sprintf(self::CHECK_NAME_PATTERN, $this->service),
            $responseStatus,
            sprintf(self::CHECK_DESCRIPTION_PATTERN, $this->service, $this->endpoint),
            $stopwatch->stop('ping')->getDuration()
        );
    }
}
