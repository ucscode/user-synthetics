<?php

namespace Uss\Component\Route;

use Symfony\Component\Routing\Route as SymfonyRoute;
use Symfony\Component\Uid\Uuid;

class Route
{
    protected string $uuid;

    protected SymfonyRoute $symfonyRoute;

    public function __construct(string $route, RouteInterface $controller, string|array $methods = [], protected int $priority = 0) 
    {
        $this->uuid = Uuid::v3(Uuid::v4(), md5(spl_object_id($this))); 

        $this->symfonyRoute = new SymfonyRoute(
            $route,
            [], // defaults
            [], // requirements
            [], // options
            '', // host
            [], // schemes
            $methods,
            '', // conditions
        );
        
        $this->registerRoute($controller);
    }

    public function getUuidString(): string
    {
        return $this->uuid;
    }

    public function getSource(): SymfonyRoute
    {
        return $this->symfonyRoute;
    }

    private function registerRoute(RouteInterface $controller): void
    {
        RouteRegistry::instance()->getRouteCollection()->add($this->uuid, $this->symfonyRoute, $this->priority);
        RouteRegistry::instance()->setController($this->uuid, $controller);
    }
}