<?php

declare(strict_types=1);

namespace Shared\Infrastructure\DependencyInjection;

use ReflectionException;
use Shared\Domain\CommandLine\CommandLine;
use Shared\Infrastructure\Resolver\Type;
use function Lambdish\Phunctional\map;

final class CommandLinesCompilerPass implements CompilerPass
{
    /**
     * @throws ReflectionException
     */
    public function process(ContainerBuilder $containerBuilder): void
    {
        $objects = map(
            fn ($class) => $containerBuilder->findDefinition($class),
            $containerBuilder->findClassesByResolver(CommandLine::class, Type::INTERFACE, 'src')
        );

        $containerBuilder->addDefinitions([CompilerPassesType::COMMAND_LINES->value => $objects]);
    }
}
