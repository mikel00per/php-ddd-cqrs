<?php

declare(strict_types=1);

namespace Shared\Domain\Bus\Event;

use DateTimeImmutable;
use DateTimeInterface;
use Shared\Domain\ValueObjects\Uuid;

abstract class DomainEvent
{
    private string $eventId;
    private string $occurredOn;

    /**
     * @psalm-suppress RiskyTruthyFalsyComparison
     */
    public function __construct(private readonly string $aggregateId, string $eventId = null, string $occurredOn = null)
    {
        $this->eventId = $eventId ?: Uuid::random()->value();
        $this->occurredOn = $occurredOn ?: (new DateTimeImmutable())->format(DateTimeInterface::ATOM);
    }

    abstract public static function fromPrimitives(
        string $aggregateId,
        array $body,
        string $eventId,
        string $occurredOn
    ): self;

    abstract public static function eventName(): string;

    abstract public function toPrimitives(): array;

    final public function aggregateId(): string
    {
        return $this->aggregateId;
    }

    final public function eventId(): string
    {
        return $this->eventId;
    }

    final public function occurredOn(): string
    {
        return $this->occurredOn;
    }
}
