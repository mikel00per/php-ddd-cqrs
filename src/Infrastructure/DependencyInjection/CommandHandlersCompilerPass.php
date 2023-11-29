<?php

declare(strict_types=1);

namespace Shared\Infrastructure\DependencyInjection;

use ReflectionException;
use Shared\Domain\Bus\Command\CommandHandler;
use Shared\Infrastructure\Resolver\Type;

final class CommandHandlersCompilerPass implements CompilerPass
{
    /**
     * @throws ReflectionException
     */
    public function process(ContainerBuilder $containerBuilder): void
    {
        $classes = $containerBuilder->findClassesByResolver(CommandHandler::class, Type::INTERFACE, 'src');

        $containerBuilder->addDefinitions([CompilerPassesType::COMMAND_HANDLERS->value => $classes]);
    }
}
