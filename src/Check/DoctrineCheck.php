<?php

declare(strict_types=1);

namespace HealthChecksBundle\Check;

use Symfony\Component\DependencyInjection\ContainerInterface;
use HealthChecksBundle\Dto\ResponseDto;
use Throwable;

class DoctrineCheck implements CheckInterface
{
    private const CHECK_RESULT_NAME = 'doctrine';

    public function __construct(private readonly ContainerInterface $container)
    {
    }

    public function check(): ResponseDto
    {
        if ($this->container->has('doctrine.orm.entity_manager') === false) {
            return new ResponseDto(self::CHECK_RESULT_NAME, 'fail', 'Entity Manager Not Found.', 0);
        }

        $entityManager = $this->container->get('doctrine.orm.entity_manager');

        if ($entityManager === null) {
            return new ResponseDto(self::CHECK_RESULT_NAME, 'fail', 'Entity Manager Not Found.', 0);
        }

        try {
            $con = $entityManager->getConnection();
            $con->executeQuery($con->getDatabasePlatform()->getDummySelectSQL())->free();
        } catch (Throwable $e) {
            return new ResponseDto(self::CHECK_RESULT_NAME, 'fail', $e->getMessage(), 0);
        }

        return new ResponseDto(self::CHECK_RESULT_NAME, '', 'ok', 0);
    }
}
