<?php

declare(strict_types=1);

namespace SymfonyHealthCheckBundle\Check;

use Symfony\Component\DependencyInjection\ContainerInterface;
use SymfonyHealthCheckBundle\Dto\ResponseDto;
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
            return new ResponseDto(self::CHECK_RESULT_NAME, 'fail', 'Entity Manager Not Found.', '0.2');
        }

        $entityManager = $this->container->get('doctrine.orm.entity_manager');

        if ($entityManager === null) {
            return new ResponseDto(self::CHECK_RESULT_NAME, 'fail', 'Entity Manager Not Found.', '0.3');
        }

        try {
            $con = $entityManager->getConnection();
            $con->executeQuery($con->getDatabasePlatform()->getDummySelectSQL())->free();
        } catch (Throwable $e) {
            return new ResponseDto(self::CHECK_RESULT_NAME, false, $e->getMessage());
        }

        return new ResponseDto(self::CHECK_RESULT_NAME, true, 'ok');
    }
}
