<?php

declare(strict_types=1);

namespace Shared\Infrastructure\Bus\Event;

use JsonException;
use Shared\Domain\Bus\Event\DomainEvent;

final class DomainEventJsonSerializer
{
    /**
     * @throws JsonException
     */
    public static function serialize(DomainEvent $domainEvent): string
    {
        return json_encode([
            'data' => [
                'id' => $domainEvent->eventId(),
                'type' => $domainEvent::eventName(),
                'occurred_on' => $domainEvent->occurredOn(),
                'attributes' => array_merge($domainEvent->toPrimitives(), ['id' => $domainEvent->aggregateId()]),
            ],
            'meta' => [],
        ], JSON_THROW_ON_ERROR);
    }
}
