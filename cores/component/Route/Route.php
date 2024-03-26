<?php

namespace Uss\Component\Route;

use Symfony\Component\Routing\Route as SymfonyRoute;
use Symfony\Component\Uid\Uuid;

class Route
{
    protected readonly string $id;

    public function __construct(string $route, RouteInterface $controller, string|array $methods = ['GET', 'POST']) 
    {
        $this->id = Uuid::v3(Uuid::v4(), md5(spl_object_id($this))); 

        RouteRegistry::instance()->getRouteCollection()->add(
            $this->id, 
            new SymfonyRoute(
                $route,
                [], // defaults
                [], // requirements
                [], // options
                '', // host
                [], // schemes
                $methods,
                '', // conditions
            ),
        );

        RouteRegistry::instance()->setController($this->id, $controller);
    }
}