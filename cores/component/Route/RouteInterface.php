<?php

namespace Uss\Component\Route;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

interface RouteInterface
{
    public function onload(Request $request): Response;
}
