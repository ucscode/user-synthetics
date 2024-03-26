<?php

namespace Uss\Component\Route;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;
use Uss\Component\Kernel\Uss;
use Uss\Component\Trait\SingletonTrait;

final class RouteRegistry implements RouteRegistryInterface
{
    use SingletonTrait;

    private RequestContext $requestContext;
    private RouteCollection $routeCollection;
    private UrlMatcher $urlMatcher;

    private array $controllers = [];

    private array $statusCodeTemplate = [
        Response::HTTP_NOT_FOUND => '@Uss/errors/layout.html.twig',
    ];

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

    public function setController(string $name, RouteInterface $controllers): bool
    {
        if($this->hasSymfonyRoute($name)) {
            $this->controllers[$name] = $controllers;
            return true;
        }
        return false;
    }

    public function getController(string $name): ?RouteInterface
    {
        return $this->hasSymfonyRoute($name) ? ($this->controllers[$name] ?? null) : null;
    }

    public function removeController(string $name): bool
    {
        if(!$this->hasSymfonyRoute($name)) {
            if(array_key_exists($name, $this->controllers)) {
                unset($this->controllers[$name]);
            }
            return true;
        }
        return false;
    }

    public function setResponseStatusTemplate(int $statusCode, string $template): self
    {
        $this->statusCodeTemplate[$statusCode] = $template;
        return $this;
    }

    public function getResponseStatusTemplate(int $statusCode): ?string
    {
        return $this->statusCodeTemplate[$statusCode] ?? null;
    }

    private function hasSymfonyRoute(string $name): bool
    {
        return !!$this->routeCollection->get($name);
    }
}