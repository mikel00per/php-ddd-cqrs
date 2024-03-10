<?php

declare(strict_types=1);

namespace Shared\Infrastructure\Bus\Query;

use Psr\Http\Message\ServerRequestInterface;
use ReflectionClass;
use ReflectionException;
use Shared\Domain\Bus\Query\Query;
use Shared\Infrastructure\Bus\MissingParameter;

final class QueryMapper
{
    /**
     * @param class-string $class
     *
     * @throws ReflectionException
     *
     * @psalm-suppress MoreSpecificReturnType
     * @psalm-suppress LessSpecificReturnStatement
     */
    public static function fromRequest(string $class, ServerRequestInterface $serverRequest): Query
    {
        $reflection = new ReflectionClass($class);

        $params = $serverRequest->getQueryParams();

        $values = [];
        foreach ((array) $reflection->getConstructor()?->getParameters() as $parameter) {
            if (!isset($params[$parameter->getName()]) && !$parameter->allowsNull()) {
                throw new MissingParameter($parameter->getName());
            }

            $values[] = $params[$parameter->getName()] ?? null;
        }

        return new $class(...$values);
    }
}
