<?php

declare(strict_types=1);

namespace Shared\Infrastructure\DependencyInjection;

enum CompilerPassesType: string
{
    case COMMAND_HANDLERS = 'command_handlers';
    case QUERY_HANDLERS = 'query_handlers';
    case DOMAIN_EVENT_SUBSCRIBERS = 'domain_event_subscribers';
}
