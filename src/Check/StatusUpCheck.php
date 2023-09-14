<?php

declare(strict_types=1);

namespace SymfonyHealthCheckBundle\Check;

use SymfonyHealthCheckBundle\Dto\Response;

class StatusUpCheck implements CheckInterface
{
    public function check(string $component): Response
    {
        return new Response($component, 'pass', 'Status up description', '0.1');
    }
}
