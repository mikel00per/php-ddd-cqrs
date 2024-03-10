<?php

declare(strict_types=1);

namespace Shared\Infrastructure\Bus\Command;

use Psr\Http\Message\ServerRequestInterface;
use ReflectionClass;
use ReflectionException;
use Shared\Domain\Bus\Command\Command;
use Shared\Infrastructure\Bus\MissingParameter;

final class CommandMapper
{
    /**
     * @param class-string $class
     *
     * @throws ReflectionException
     *
     * @psalm-suppress MoreSpecificReturnType
     * @psalm-suppress LessSpecificReturnStatement
     */
    public static function fromRequest(string $class, ServerRequestInterface $serverRequest): Command
    {
        $reflection = new ReflectionClass($class);

        $queryParams = $serverRequest->getQueryParams();
        $bodyParams = (array) $serverRequest->getParsedBody();
        $attributes = $serverRequest->getAttributes();

        $params = array_merge($queryParams, $bodyParams, $attributes);

        $values = [];
        foreach ((array) $reflection->getConstructor()?->getParameters() as $parameter) {
            if (!isset($params[$parameter->getName()]) && !$parameter->allowsNull()) {
                throw new MissingParameter($parameter->getName());
            }

            $values[] = $params[$parameter->getName()] ?? null;
        }

        /**  */
        return new $class(...$values);
    }
}
