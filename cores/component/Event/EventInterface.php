<?php

namespace Uss\Component\Event;

interface EventInterface
{
    public function eventAction(mixed $data): void;
}
