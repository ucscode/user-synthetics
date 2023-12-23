<?php

namespace Uss\Component\Event;

interface EventInterface
{
    public function eventAction(array|object $data): void;
}
