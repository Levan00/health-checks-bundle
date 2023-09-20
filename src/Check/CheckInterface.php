<?php

declare(strict_types=1);

namespace SymfonyHealthCheckBundle\Check;

use SymfonyHealthCheckBundle\Dto\ResponseDto;

interface CheckInterface
{
    public function check(): ResponseDto;
}
