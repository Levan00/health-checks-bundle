<?php

declare(strict_types=1);

namespace Ringostat\HealthChecksBundle\Check;

use MongoDB\Client;
use MongoDB\Driver\Exception\ConnectionTimeoutException;
use Ringostat\HealthChecksBundle\Dto\ResponseDto;
use Ringostat\HealthChecksBundle\Enum\Status;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Stopwatch\Stopwatch;

class MongoDbConnectionCheck implements CheckInterface
{
    private const CHECK_NAME = 'mongodb:connection';
    private const CHECK_DESCRIPTION = 'Checking connection with MongoDB';

    public function __construct(private readonly ContainerInterface $container)
    {
    }

    public function check(): ResponseDto
    {
        $stopwatch = new Stopwatch(true);

        $stopwatch->start('mongodb_connection');

        if ($this->container->has('doctrine_mongodb') === false) {
            return new ResponseDto(
                self::CHECK_NAME,
                Status::FAIL,
                self::CHECK_DESCRIPTION,
                $stopwatch->stop('mongodb_connection')->getDuration()
            );
        }

        $managerRegistry = $this->container->get('doctrine_mongodb');

        /** @var Client $connection */
        foreach ($managerRegistry->getConnections() as $connection) {
            try {
                $connection->listDatabases();
            } catch (ConnectionTimeoutException) {
                return new ResponseDto(
                    self::CHECK_NAME,
                    Status::FAIL,
                    self::CHECK_DESCRIPTION,
                    $stopwatch->stop('mongodb_connection')->getDuration()
                );
            }
        }

        return new ResponseDto(
            self::CHECK_NAME,
            Status::PASS,
            self::CHECK_DESCRIPTION,
            $stopwatch->stop('mongodb_connection')->getDuration()
        );
    }
}
