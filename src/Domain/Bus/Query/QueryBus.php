<?php

namespace Ddd\Domain\Bus\Query;

interface QueryBus
{
    public function ask(Query $query): ?Response;
}
