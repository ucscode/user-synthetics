<?php

namespace Uss\Component\Route;

use Uss\Component\Kernel\Uss;

class Route
{
    public readonly string $route;
    public readonly string $request;
    public readonly string $path;
    public readonly string $query;
    public readonly array $methods;
    public readonly array $regexMatches;
    public readonly bool $isAuthorized;

    protected ?array $backtrace;
    protected RouteInterface $controller;

    private static array $inventories = [];

    public function __construct(string $route, RouteInterface $controller, string|array $methods = ['GET', 'POST']) 
    {
        $this->controller = $controller;
        $this->normalizeRequestMethods($methods);
        $this->bootstrap($route);
        $this->authorization();
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
    }

    protected function normalizeRequestMethods(string|array $methods): void
    {
        !is_string($methods) ?: $methods = [trim($methods)];

        $standardMethods = [
            'CONNECT',
            'DELETE',
            'GET', 
            'HEAD',
            'OPTIONS',
            'PATCH',
            'POST',
            'PUT',
            'TRACE',
        ];

        $this->methods = array_intersect(
            $standardMethods,
            array_map(fn ($value) => is_string($value) ? strtoupper(trim($value)) : null, $methods)
        );
    }

    protected function authorization(): void
    {
        $this->isAuthorized = 
            !!preg_match('#^' . $this->route . '$#i', $this->path, $matches) &&
            !empty($this->methods) &&
            in_array($_SERVER['REQUEST_METHOD'], $this->methods, true);
        $this->regexMatches = $matches;
    }

    protected function processRouter(): void
    {
        $this->backtraceRouterSource();
        self::$inventories[] = $this;
        if($this->isAuthorized) {
            $this->controller->onload([
                'matches' => $this->regexMatches,
                'route' => $this,
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