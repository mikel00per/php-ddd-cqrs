<?php

declare(strict_types=1);

namespace Shared\Infrastructure\DependencyInjection;

use ReflectionException;
use Shared\Domain\CommandLine\CommandLine;
use Shared\Infrastructure\Resolver\Type;

final class CommandLinesCompilerPass implements CompilerPass
{
    /**
     * @throws ReflectionException
     */
    public function process(ContainerBuilder $containerBuilder): void
    {
        $classes = $containerBuilder->findClassesByResolver(CommandLine::class, Type::INTERFACE, 'src');

        $containerBuilder->addDefinitions([CompilerPassesType::COMMAND_LINES->value => $classes]);
    }
}
