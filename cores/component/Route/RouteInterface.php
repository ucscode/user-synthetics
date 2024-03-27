<?php

namespace Uss\Component\Route;

use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Response;

interface RouteInterface
{
    public function onload(ParameterBag $container): Response;
}
