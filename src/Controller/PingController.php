<?php

declare(strict_types=1);

namespace SymfonyHealthCheckBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

final class PingController extends AbstractController
{
    #[Route('/ping', name: 'ping', methods: ['GET'])]
    public function pingAction(): JsonResponse
    {
        return new JsonResponse('pong');
    }
}
