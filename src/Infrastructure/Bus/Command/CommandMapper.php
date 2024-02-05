<?php

declare(strict_types=1);

namespace Shared\Infrastructure\Bus\Command;

use Psr\Http\Message\ServerRequestInterface;
use ReflectionClass;
use ReflectionException;
use ReflectionParameter;
use Shared\Domain\Bus\Query\Query;
use Shared\Infrastructure\Bus\MissingParameter;
use function DI\value;

final class CommandMapper
{
    /**
     * @throws ReflectionException
     */
    public static function fromRequest(string $class, ServerRequestInterface $serverRequest): Query
    {
        $reflection = new ReflectionClass($class);

        $queryParams = $serverRequest->getQueryParams();
        $bodyParams = $serverRequest->getParsedBody() ?? [];

        $params = array_merge($queryParams, $bodyParams ?? []);

        $values = [];
        foreach ((array) $reflection->getConstructor()?->getParameters() as $parameter) {
            assert($parameter instanceof ReflectionParameter);

            if (!isset($params[$parameter->getName()]) && !$parameter->allowsNull()) {
                throw new MissingParameter($parameter->getName());
            }

            $values[] = $params[$parameter->getName()] ?? null;
        }

        return new $class(...$values);
    }
}