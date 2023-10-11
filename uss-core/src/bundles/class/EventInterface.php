<?php

namespace Uss;

interface EventInterface
{
    public function eventAction(array $data): void;
}
