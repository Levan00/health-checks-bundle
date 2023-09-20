<?php

declare(strict_types=1);

namespace SymfonyHealthCheckBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Stopwatch\Stopwatch;
use SymfonyHealthCheckBundle\Check\CheckInterface;
use SymfonyHealthCheckBundle\Dto\HealthCheckDto;
use SymfonyHealthCheckBundle\Enum\Status;

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
        $resultHealthCheck = [
            'status' => Status::PASS,
            'version' => 'version from git',
            'duration' => '',
            'time' => (new \DateTime())->format(''),
            'checks' => [],
        ];

        $stopwatch = new Stopwatch();
        $stopwatch->start('duration');

        foreach ($this->healthChecks as $healthCheck) {
            $response = $healthCheck->check();

            $resultHealthCheck['checks'][] += $response->toArray();

            if ($response->getStatus() === Status::FAIL) {
                $resultHealthCheck['status'] = Status::FAIL;
            } elseif ($response->getStatus() === Status::WARNING && $resultHealthCheck['status'] === Status::PASS) {
                $resultHealthCheck['status'] = Status::WARNING;
            }
        }

        return new JsonResponse($resultHealthCheck, Response::HTTP_OK);
    }
}
