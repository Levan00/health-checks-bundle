<?php

declare(strict_types=1);

namespace SymfonyHealthCheckBundle\Check;

use SymfonyHealthCheckBundle\Dto\ResponseDto;

class StatusUpCheck implements CheckInterface
{
    public function check(): ResponseDto
    {
        return new ResponseDto($name, 'pass', 'Status up description', '0.1');
    }
}
