<?php

namespace Ucscode\Event;

interface EventInterface
{
    public function eventAction(array $data): void;
}
