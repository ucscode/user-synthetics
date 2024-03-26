<?php

namespace Uss\Component\Route;

use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;

interface RouteRegistryInterface
{
    public function getRouteCollection(): RouteCollection;
    public function getRequestContext(): RequestContext;
    public function getUrlMatcher(): UrlMatcher;

    public function setController(string $name, RouteInterface $controllers): bool;
    public function getController(string $name): ?RouteInterface;
    public function removeController(string $name): bool;

    public function setResponseStatusTemplate(int $statusCode, string $template): self;
    public function getResponseStatusTemplate(int $statusCode): ?string;
}