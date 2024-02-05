<?php

namespace Shared\Infrastructure\Bus;

use DomainException;

final class MissingParameter extends DomainException
{
    protected $message = 'Missing parameter';

    public function __construct(string $parameterName)
    {
        parent::__construct("$this->message $parameterName");
    }
}