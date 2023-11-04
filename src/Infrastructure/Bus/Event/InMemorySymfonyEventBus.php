<?php

declare(strict_types=1);

namespace Ddd\Infrastructure\Bus\Event;

use Ddd\Domain\Bus\Event\DomainEvent;
use Ddd\Domain\Bus\Event\EventBus;
use Ddd\Infrastructure\Bus\CallableFirstParameterExtractor;
use Exception;
use Symfony\Component\Messenger\Exception\NoHandlerForMessageException;
use Symfony\Component\Messenger\Handler\HandlersLocator;
use Symfony\Component\Messenger\MessageBus;
use Symfony\Component\Messenger\Middleware\HandleMessageMiddleware;

class InMemorySymfonyEventBus implements EventBus
{
    private MessageBus $bus;

    public function __construct(iterable $subscribers)
    {
        $handlers = CallableFirstParameterExtractor::forPipedCallables($subscribers);
        $handlersLocator = new HandlersLocator($handlers);
        $middlewareHandler = new HandleMessageMiddleware($handlersLocator);

        $this->bus = new MessageBus([$middlewareHandler]);
    }

    public function publish(DomainEvent ...$events): void
    {
        foreach ($events as $event) {
            try {
                $this->bus->dispatch($event);
            } catch (NoHandlerForMessageException) {
            }
        }
    }
}
