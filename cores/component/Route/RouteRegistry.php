<?php

namespace Uss\Component\Route;

use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;
use Uss\Component\Kernel\Uss;
use Uss\Component\Trait\SingletonTrait;

final class RouteRegistry
{
    use SingletonTrait;

    private RequestContext $requestContext;
    private RouteCollection $routeCollection;
    private UrlMatcher $urlMatcher;

    private array $controllers = [];

    public function __construct()
    {
        $this->routeCollection = new RouteCollection();
        $this->requestContext = new RequestContext();
        $this->requestContext->fromRequest(Uss::instance()->request);
        $this->urlMatcher = new UrlMatcher($this->routeCollection, $this->requestContext);
    }

    public function getRouteCollection(): RouteCollection
    {
        return $this->routeCollection;
    }

    public function getRequestContext(): RequestContext
    {
        return $this->requestContext;
    }

    public function getUrlMatcher(): UrlMatcher
    {
        return $this->urlMatcher;
    }

    public function setController(string $name, RouteInterface $controllers): self
    {
        if($this->hasSymfonyRoute($name)) {
            $this->controllers[$name] = $controllers;
        }
        return $this;
    }

    public function getController(string $name): ?RouteInterface
    {
        return $this->hasSymfonyRoute($name) ? ($this->controllers[$name] ?? null) : null;
    }

    public function removeController(string $name): bool
    {
        if($this->hasSymfonyRoute($name)) {
            return false;
        }

        if(array_key_exists($name, $this->controllers)) {
            unset($this->controllers[$name]);
        }
    }

    private function hasSymfonyRoute(string $name): bool
    {
        return !!$this->routeCollection->get($name);
    }
}