<?php

declare(strict_types=1);

namespace Shared\Infrastructure\Bus\Event\MySql;

use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;
use Exception;
use RuntimeException;
use Shared\Domain\Bus\Event\DomainEvent;
use Shared\Infrastructure\Bus\Event\DomainEventMapping;

use function Lambdish\Phunctional\each;
use function Lambdish\Phunctional\map;

final readonly class MySqlDoctrineDomainEventsConsumer
{
    private Connection $connection;

    public function __construct(EntityManager $entityManager, private DomainEventMapping $eventMapping)
    {
        $this->connection = $entityManager->getConnection();
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function consume(callable $subscribers, int $eventsToConsume): void
    {
        $events = $this->connection
            ->executeQuery("SELECT * FROM domain_events ORDER BY occurred_on ASC LIMIT $eventsToConsume")
            ->fetchAllAssociative();

        each($this->executeSubscribers($subscribers), $events);

        $ids = implode(', ', map($this->idExtractor(), $events));

        if (!empty($ids)) {
            $this->connection->executeStatement("DELETE FROM domain_events WHERE id IN ($ids)");
        }
    }

    private function executeSubscribers(callable $subscribers): callable
    {
        return function (array $rawEvent) use ($subscribers): void {
            try {
                /** @var class-string<DomainEvent> $domainEventClass */
                $domainEventClass = $this->eventMapping->for($rawEvent['name']);
                $domainEvent = $domainEventClass::fromPrimitives(
                    $rawEvent['aggregate_id'],
                    json_decode($rawEvent['body'], true, 512, JSON_THROW_ON_ERROR),
                    $rawEvent['id'],
                    $this->formatDate($rawEvent['occurred_on'])
                );

                $subscribers($domainEvent);
            } catch (RuntimeException) {
            }
        };
    }

    /**
     * @throws Exception
     */
    private function formatDate(mixed $stringDate): string
    {
        return (new DateTimeImmutable($stringDate))->format(DateTimeInterface::ATOM);
    }

    private function idExtractor(): callable
    {
        return static fn (array $event): string => "'{$event['id']}'";
    }
}
