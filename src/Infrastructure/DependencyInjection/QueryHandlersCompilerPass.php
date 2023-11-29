<?php

declare(strict_types=1);

namespace Shared\Infrastructure\DependencyInjection;

use ReflectionException;
use Shared\Domain\Bus\Query\QueryHandler;
use Shared\Infrastructure\Resolver\Type;

final class QueryHandlersCompilerPass implements CompilerPass
{
    /**
     * @throws ReflectionException
     */
    public function process(ContainerBuilder $containerBuilder): void
    {
        $classes = $containerBuilder->findClassesByResolver(QueryHandler::class, Type::INTERFACE, 'src');

        $containerBuilder->addDefinitions([CompilerPassesType::QUERY_HANDLERS->value => $classes]);
    }
}
