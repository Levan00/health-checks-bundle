<?php

declare(strict_types=1);

namespace Ringostat\HealthChecksBundle\Check;

use Ringostat\HealthChecksBundle\Dto\ResponseDto;

interface CheckInterface
{
    public function check(): ResponseDto;
}
