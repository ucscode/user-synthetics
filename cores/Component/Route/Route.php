<?php

namespace Uss\Component\Route;

use Uss\Component\Kernel\Uss;

class Route
{
    protected readonly string $route;
    protected readonly string $request;
    protected readonly string $path;
    protected readonly string $query;
    protected readonly array $methods;
    protected readonly array $regexMatches;
    protected readonly bool $isAuthorized;
    protected ?array $backtrace;
    protected RouteInterface $controller;
    private static array $inventories = [];

    public function __construct(string $route, RouteInterface $controller, array $methods = ['GET', 'POST']) 
    {
        $this->controller = $controller;
        $this->bootstrap($route);
        $this->normalizeRequestMethods($methods);
        $this->processRouter();
    }

    final public static function getInventories(bool $authentic = false): array
    {
        return $authentic ?
            array_filter(self::$inventories, fn ($route) => $route->isAuthorized) :
            self::$inventories;
    }

    protected function bootstrap(string $route): void
    {
        $uss = Uss::instance();
        $this->route = $uss->filterContext($route);
        $this->path = $uss->filterContext($uss->getUrlSegments());
        $this->query = parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY) ?? '';
        $this->request = $this->path . '?' . $this->query;
        $this->isAuthorized = (bool)preg_match('#^' . $this->route . '$#i', $this->path, $matches);
        $this->regexMatches = $matches;
    }

    protected function normalizeRequestMethods(array $methods): void
    {
        $standardMethods = ['GET', 'POST', 'DELETE', 'PUT', 'PATCH'];
        $this->methods = array_intersect(
            $standardMethods,
            array_map(fn ($value) => is_string($value) ? strtoupper(trim($value)) : null, $methods)
        );
    }

    protected function processRouter(): void
    {
        $this->backtraceRouterSource();
        self::$inventories[] = $this;
        if(!empty($this->methods) && $this->isAuthorized) {
            $this->controller->onload([
                'matches' => $this->regexMatches
            ]);
        }
    }

    protected function backtraceRouterSource(): void
    {
        foreach(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS) as $key => $currentTrace) {
            if(($currentTrace['class'] ?? null) === self::class) {
                if(strtolower($currentTrace['function']) === '__construct') {
                    $this->backtrace = $currentTrace;
                    return;
                };
            };
        };
        $this->backtrace = null;
    }

}