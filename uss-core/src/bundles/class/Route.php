<?php

class Route {

    private string $route;
    private array $methods;
    private string $request;
    private array $regexMatches;
    private bool $isAuthentic;
    private RouteInterface|string $controller;
    private $backtrace;

    public function __construct(string $route, RouteInterface|string $controller, array|string $methods = 'GET'
    ) {
        $this->route = $route;
        $this->resolveController($controller, __METHOD__);
        $relianceArray =  $this->regulateMethods($methods);
        $this->isAuthentic = !in_array(false, $this->resolveRoute($relianceArray));
    }

    public function __get($key)
    {
        return $this->{$key} ?? null;
    }

    public function loadController(mixed ...$args): void
    {
        if($this->isAuthentic) 
        {
            $controller = $this->controller;

            if(is_string($controller)) {
                $controller = new $controller(...$args);
            }

            $controller->onload(
                $this->regexMatches ?? []
            );
        };
    }

    private function resolveController(RouteInterface|string $controller, string $constructor): void
    {
        if(is_string($controller)) {
            if(!in_array(RouteInterface::class, class_implements($controller))) {
                throw new \Exception(
                    sprintf(
                        "%s Error: Controller in argument 2 must implement '%s'",
                        $constructor,
                        RouteInterface::class
                    )
                );
            };
        };
        $this->controller = $controller;
    }

    private function regulateMethods(array|string $methods): array
    {
        $requestMethods = ['GET', 'POST', 'DELETE', 'PUT', 'PATCH'];

        if(!is_array($methods)) {
            $methods = [$this->methods];
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

        $route = $uss->filterContext($this->route);

        $this->request = $uss->filterContext($uss->splitUri());

        # Compare the request path to the current URL
        $relianceArray[] = !!preg_match('~^' . $route . '$~i', $this->request, $result);

        $this->regexMatches = $result;

        /** Execute routing event */
        $this->debugRouter();

        return $relianceArray;
    }

    private function debugRouter()
    {
        $debugBacktrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
        foreach($debugBacktrace as $key => $currentTrace) {
            if($key > 255) {
                break;
            } elseif(($currentTrace['class'] ?? null) === Uss::instance()::class) {
                if(strtolower($currentTrace['function']) === 'route') {
                    $this->backtrace = $currentTrace;
                };
            };
        };
    }

}