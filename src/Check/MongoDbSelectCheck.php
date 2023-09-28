<?php

declare(strict_types=1);

namespace HealthChecksBundle\Check;

use App\Entity\Project;
use MongoDB\Client;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Stopwatch\Stopwatch;
use HealthChecksBundle\Dto\ResponseDto;
use HealthChecksBundle\Enum\Status;
use Throwable;

class MongoDbSelectCheck implements CheckInterface
{
    private const CHECK_NAME = 'mongodb:select';
    private const CHECK_DESCRIPTION = 'Checking a select query in MongoDB';

    public function __construct(private readonly ContainerInterface $container)
    {
    }

    public function check(): ResponseDto
    {
        $stopwatch = new Stopwatch(true);

        $stopwatch->start('mongodb_select');

        if ($this->container->has('doctrine_mongodb') === false) {
            return new ResponseDto(
                self::CHECK_NAME,
                Status::FAIL,
                self::CHECK_DESCRIPTION,
                $stopwatch->stop('mongodb_connection')->getDuration()
            );
        }

        $entityManager = $this->container->get('doctrine_mongodb');

        $repository = $entityManager->getManager()->getRepository(Project::class);

        try {
            $record = $repository->findBy([], ['_id' => 'ASC'], 1);
        } catch (Throwable) {
            return new ResponseDto(
                self::CHECK_NAME,
                Status::FAIL,
                self::CHECK_DESCRIPTION,
                $stopwatch->stop('mongodb_select')->getDuration()
            );
        }

        return new ResponseDto(
            self::CHECK_NAME,
            empty($record) ? Status::WARNING : Status::PASS,
            self::CHECK_DESCRIPTION,
            $stopwatch->stop('mongodb_select')->getDuration()
        );
    }
}
