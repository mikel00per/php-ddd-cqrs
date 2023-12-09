<?php

declare(strict_types=1);

namespace Shared\Infrastructure\Bus\Event\RabbitMq;

use Shared\Domain\Bus\Event\DomainEventSubscriber;

use function Lambdish\Phunctional\last;
use function Lambdish\Phunctional\map;

final class RabbitMqQueueNameFormatter
{
    public static function format(DomainEventSubscriber $subscriber): string
    {
        $subscriberClassPaths = explode('\\', $subscriber::class);

        $queueNameParts = [
            $subscriberClassPaths[0],
            $subscriberClassPaths[1],
            $subscriberClassPaths[2],
            last($subscriberClassPaths),
        ];

        return implode('.', map(self::toSnakeCase(), $queueNameParts));
    }

    public static function formatRetry(DomainEventSubscriber $subscriber): string
    {
        $queueName = self::format($subscriber);

        return "retry.$queueName";
    }

    public static function formatDeadLetter(DomainEventSubscriber $subscriber): string
    {
        $queueName = self::format($subscriber);

        return "dead_letter.$queueName";
    }

    public static function shortFormat(DomainEventSubscriber $subscriber): string
    {
        $subscriberCamelCaseName = (string) last(explode('\\', $subscriber::class));

        $string = (string) preg_replace('/([^A-Z\s])([A-Z])/', '$1_$2', $subscriberCamelCaseName);

        return ctype_lower($subscriberCamelCaseName) ? $subscriberCamelCaseName : strtolower($string);
    }

    private static function toSnakeCase(): callable
    {
        return static fn (string $text): string =>
            ctype_lower($text) ? $text : strtolower((string) preg_replace('/([^A-Z\s])([A-Z])/', '$1_$2', $text));
    }
}
