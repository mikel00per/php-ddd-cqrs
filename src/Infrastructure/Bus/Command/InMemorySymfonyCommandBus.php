<?php

declare(strict_types=1);

namespace Shared\Infrastructure\Bus\Command;

use Shared\Domain\Bus\Command\Command;
use Shared\Domain\Bus\Command\CommandBus;
use Shared\Infrastructure\Bus\CallableFirstParameterExtractor;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\Exception\NoHandlerForMessageException;
use Symfony\Component\Messenger\Handler\HandlersLocator;
use Symfony\Component\Messenger\MessageBus;
use Symfony\Component\Messenger\Middleware\HandleMessageMiddleware;
use Throwable;

final class InMemorySymfonyCommandBus implements CommandBus
{
    private MessageBus $bus;

    public function __construct(iterable $commandHandlers)
    {
        $handlers = CallableFirstParameterExtractor::forCallables($commandHandlers);
        $handlersLocator = new HandlersLocator($handlers);
        $middlewareHandler = new HandleMessageMiddleware($handlersLocator);

        $this->bus = new MessageBus([$middlewareHandler]);
    }

    /**
     * @throws Throwable
     */
    public function dispatch(Command $command): void
    {
        try {
            $this->bus->dispatch($command);
        } catch (NoHandlerForMessageException) {
            throw new CommandNotRegisteredError($command);
        } catch (HandlerFailedException $error) {
            throw $error->getPrevious() ?? $error;
        }
    }
}
