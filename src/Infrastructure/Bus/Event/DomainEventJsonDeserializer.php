<?php

declare(strict_types=1);

namespace Shared\Infrastructure\Bus\Event;

use JsonException;
use Shared\Domain\Bus\Event\DomainEvent;

final readonly class DomainEventJsonDeserializer
{
    public function __construct(private DomainEventMapping $mapping) {}

    /**
     * @throws JsonException
     */
    public function deserialize(string $domainEvent): DomainEvent
    {
        $eventData = json_decode($domainEvent, true, 512, JSON_THROW_ON_ERROR);
        $eventName = $eventData['data']['type'];

        /** @var class-string<DomainEvent> $eventClass */
        $eventClass = $this->mapping->for($eventName);

        return $eventClass::fromPrimitives(
            $eventData['data']['attributes']['id'],
            $eventData['data']['attributes'],
            $eventData['data']['id'],
            $eventData['data']['occurred_on']
        );
    }
}
