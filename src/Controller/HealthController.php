<?php

declare(strict_types=1);

namespace Ringostat\HealthChecksBundle\Controller;

use DateTime;
use Ringostat\HealthChecksBundle\Check\CheckInterface;
use Ringostat\HealthChecksBundle\Enum\Status;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Stopwatch\Stopwatch;

final class HealthController extends AbstractController
{
    /**
     * @var array<CheckInterface>
     */
    private array $healthChecks = [];

    public function addHealthCheck(CheckInterface $healthCheck): void
    {
        $this->healthChecks[] = $healthCheck;
    }

    #[Route('/health', name: 'health', methods: ['GET'])]
    public function healthCheckAction(): JsonResponse
    {
        $result = [
            'status' => Status::PASS,
            'version' => 'version from git',
            'time' => (new DateTime())->format('Y-m-d H:i:s'),
            'duration' => 0,
            'checks' => [],
        ];

        $stopwatch = new Stopwatch();
        $stopwatch->start('health_check');

        foreach ($this->healthChecks as $healthCheck) {
            $response = $healthCheck->check();

            $result['checks'] += $response->toArray();

            $result['status'] = match (true) {
                $response->getStatus() === Status::FAIL => Status::FAIL,
                $response->getStatus() === Status::WARNING && $result['status'] === Status::PASS => Status::WARNING,
                default => $result['status'],
            };
        }

        $result['duration'] = DateTime::createFromFormat('U.u', strval($stopwatch->stop('health_check')->getDuration() / 1000))->format("H:i:s.u");

        return new JsonResponse($result, Response::HTTP_OK);
    }
}
