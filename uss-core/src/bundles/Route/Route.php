<?php

class Route
{
    private static array $inventories = [];
    private string $route;
    private array $methods;
    private string $path;
    private string $query;
    private string $request;
    private array $regexMatches;
    private bool $isAuthentic;
    private RouteInterface $controller;
    private ?array $backtrace;

    public function __construct(
        string $route,
        RouteInterface $controller,
        array|string $methods = ['GET', 'POST']
    ) {
        $this->route = $route;
        $this->controller = $controller;
        $confidence =  $this->regulateMethods($methods);
        $this->configureRoute($confidence);
    }

    public function __get($key)
    {
        return $this->{$key} ?? null;
    }

    /**
     * Get the current focus expression or list of focus expressions.
    */
    public static function getInventories(bool $authentic = false): array
    {
        $routes = self::$inventories;
        if($authentic) {
            $routes = array_filter($routes, function ($route) {
                return $route->isAuthentic;
            });
        };
        return $routes;
    }

    private function configureRoute(array $confidence): void
    {
        $this->isAuthentic = !in_array(false, $this->resolveRoute($confidence));
        $this->debugRouter();
        self::$inventories[] = $this;
        $this->loadController();
    }

    private function loadController(): void
    {
        if($this->isAuthentic) {
            $this->controller->onload(
                $this->regexMatches ?? []
            );
        };
    }

    private function regulateMethods(array|string $methods): array
    {
        $requestMethods = ['GET', 'POST', 'DELETE', 'PUT', 'PATCH'];

        if(!is_array($methods)) {
            $methods = [$methods];
        };

        $this->methods = $methods;

        # Map Methods
        $this->methods = array_map(function ($value) {
            return is_string($value) ? strtoupper($value) : $value;
        }, $this->methods);

        # Filter Methods
        $this->methods = array_unique(array_filter($this->methods, function ($value) use ($requestMethods) {
            return is_string($value) && in_array($value, $requestMethods);
        }));

        # Resolve Method
        return [
            in_array($_SERVER['REQUEST_METHOD'], $this->methods)
        ];
    }

    private function resolveRoute(array $relianceArray): array
    {
        $uss = Uss::instance();

        $this->route = $uss->filterContext($this->route);
        $this->path = $uss->filterContext($uss->splitUri());
        $this->query = parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY) ?? '';
        $this->request = $this->path . '?' . $this->query;

        # Compare the request path to the current URL
        $relianceArray[] = !!preg_match('~^' . $this->route . '$~i', $this->path, $result);

        $this->regexMatches = $result;

        return $relianceArray;
    }

    private function debugRouter()
    {
        $debugBacktrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
        foreach($debugBacktrace as $key => $currentTrace) {
            if($key > 75) {
                break;
            } elseif(($currentTrace['class'] ?? null) === self::class) {
                if(strtolower($currentTrace['function']) === '__construct') {
                    $this->backtrace = $currentTrace;
                };
            };
        };
    }

}
