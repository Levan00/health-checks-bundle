<?php

declare(strict_types=1);

namespace HealthChecksBundle\Check;

use HealthChecksBundle\Dto\ResponseDto;

interface CheckInterface
{
    public function check(): ResponseDto;
}
