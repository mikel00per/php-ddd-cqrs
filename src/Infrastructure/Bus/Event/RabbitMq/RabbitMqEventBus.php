<?php

declare(strict_types=1);

namespace Shared\Infrastructure\Bus\Event\RabbitMq;

use AMQPChannelException;
use AMQPConnectionException;
use AMQPException;
use AMQPExchangeException;
use JsonException;
use Shared\Domain\Bus\Event\DomainEvent;
use Shared\Domain\Bus\Event\EventBus;
use Shared\Infrastructure\Bus\Event\DomainEventJsonSerializer;
use Shared\Infrastructure\Bus\Event\MySql\MySqlDoctrineEventBus;

use function Lambdish\Phunctional\each;

final readonly class RabbitMqEventBus implements EventBus
{
    public function __construct(
        private RabbitMqConnection $connection,
        private string $exchangeName,
        private MySqlDoctrineEventBus $failOverPublisher
    ) {}

    public function publish(DomainEvent ...$events): void
    {
        each($this->publisher(), $events);
    }

    private function publisher(): callable
    {
        return function (DomainEvent $event): void {
            try {
                $this->publishEvent($event);
            } catch (AMQPException) {
                $this->failOverPublisher->publish($event);
            }
        };
    }

    /**
     * @throws AMQPExchangeException|AMQPChannelException|AMQPConnectionException|JsonException
     */
    private function publishEvent(DomainEvent $event): void
    {
        $body = DomainEventJsonSerializer::serialize($event);
        $routingKey = $event::eventName();
        $messageId = $event->eventId();

        $this->connection->exchange($this->exchangeName)->publish(
            $body,
            $routingKey,
            AMQP_NOPARAM,
            [
                'message_id' => $messageId,
                'content_type' => 'application/json',
                'content_encoding' => 'utf-8',
            ]
        );
    }
}
