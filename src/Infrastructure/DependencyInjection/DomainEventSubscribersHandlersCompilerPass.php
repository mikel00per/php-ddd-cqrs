<?php

declare(strict_types=1);

namespace Shared\Infrastructure\DependencyInjection;

use ReflectionException;
use Shared\Domain\Bus\Command\CommandHandler;
use Shared\Domain\Bus\Event\DomainEventSubscriber;
use Shared\Infrastructure\Resolver\Type;
use function Lambdish\Phunctional\map;

final class DomainEventSubscribersHandlersCompilerPass implements CompilerPass
{
    /**
     * @throws ReflectionException
     */
    public function process(ContainerBuilder $containerBuilder): void
    {
        $objects = map(
            fn ($class) => $containerBuilder->findDefinition($class),
            $containerBuilder->findClassesByResolver(DomainEventSubscriber::class, Type::INTERFACE, 'src')
        );

        $containerBuilder->addDefinitions([CompilerPassesType::DOMAIN_EVENT_SUBSCRIBERS->value => $objects]);
    }
}
