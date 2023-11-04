<?php

namespace Ddd\Domain\Bus\Event;

interface EventBus
{
    public function publish(DomainEvent ...$events): void;
}
