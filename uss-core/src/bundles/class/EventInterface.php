<?php

interface EventInterface
{
    public function eventAction(array|object $data): void;
}
