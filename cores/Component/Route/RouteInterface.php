<?php

namespace Uss\Component\Route;

interface RouteInterface
{
    public function onload(array $context): void;
}
