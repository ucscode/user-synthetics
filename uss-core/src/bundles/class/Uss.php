<?php
/**
 * The central class for managing User Synthetics application
 *
 * User Synthetics is a web development system or framework designed to facilitate the efficient and effective building of professional web applications. It aims to streamline the development process by combining the flexibility of PHP programming language with pre-built components and extensive library integration.
 *
 * > User Synthetics requires PHP version 7.4 or higher due to its reliance on typed properties, which are essential for maintaining the integrity of relevant properties and preventing structure changes.
 *
 * @package Uss
 * @author Ucscode
 */
final class Uss extends AbstractUss
{
    use SingletonTrait;

    protected function __construct()
    {
        parent::__construct();
    }

    /**
     * Render A Twig Template
     *
     * @param string $templateFile: Reference to the twig template.
     * @param array $variables: A list of variables that will be passed to the template
     *
     * @return void
     */
    public function render(string $templateFile, array $variables = []): void
    {
        $templateFile = $this->refactorNamespace($templateFile);

        $twig = new \Twig\Environment($this->twigLoader, [
            'debug' => UssEnum::DEBUG
        ]);

        if(UssEnum::DEBUG) {
            $twig->addExtension(new \Twig\Extension\DebugExtension());
        };

        $twig->addGlobal($this->namespace, new \UssTwigGlobalExtension($this->namespace));

        foreach($this->twigExtensions as $extension) {
            $twig->addExtension(new $extension());
        }

        print($twig->render($templateFile, $variables));

        die();
    }

    /**
     * Set the focus on a specific URL path and execute a function based on the URL match.
     */
    public function route(string $route, callable|RouteInterface $controller, $methods = null): bool|object
    {
        $router = new class ($route, $controller, $methods) {
            public readonly array $requestMatch;

            protected $request;
            private array|bool $authentic = [];
            private $backtrace;

            public function __construct(
                protected string $route,
                public $controller,
                protected array|string|null $methods
            ) {
                $this->filterMethods();
                $this->resolveRoute();
                $this->authentic = !in_array(false, $this->authentic);
            }

            public function __get($key)
            {
                return $this->{$key} ?? null;
            }

            protected function filterMethods()
            {
                # PHP Default Request Methods
                $requestMethods = ['GET', 'POST', 'DELETE', 'PUT', 'PATCH'];

                # Configure Methods
                if(!is_array($this->methods)) {
                    if(is_null($this->methods)) {
                        $this->methods = $requestMethods;
                    } else {
                        $this->methods = [$this->methods];
                    };
                };

                # Map Methods
                $this->methods = array_map(function ($value) {
                    return is_string($value) ? strtoupper($value) : $value;
                }, $this->methods);

                # Filter Methods
                $this->methods = array_unique(array_filter($this->methods, function ($value) use ($requestMethods) {
                    return in_array($value, $requestMethods);
                }));

                # Resolve Method
                $this->authentic[] = in_array($_SERVER['REQUEST_METHOD'], $this->methods);
            }

            protected function resolveRoute()
            {
                # The route
                $route = Uss::instance()->filterContext($this->route);

                # The request
                $this->request = Uss::instance()->filterContext(Uss::instance()->splitUri());

                # Compare the request path to the current URL
                $this->authentic[] = !!preg_match('~^' . $route . '$~i', $this->request, $result);
                $this->requestMatch = $result;

                /** Execute routing event */
                $this->debugRouter();
            }

            protected function debugRouter()
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

        };

        self::$routes[] = $router;

        if($router->authentic) {
            if($router->controller instanceof RouteInterface) {
                $router->controller->onload($router->requestMatch ?? []);
            } else {
                call_user_func($router->controller, $router->requestMatch ?? []);
            };
            return $router;

        };

        return false;

    }

    /**
     * Retrieve URL request path segments.
     *
     * @param int|null $index Optional: index of the segment to retrieve. If not provided, returns the entire array of segments.
     * @return array|string|null The array of URL path segments if no index is provided, the segment at the specified index, or `null` if the index is out of range or the request string is not set.
     */
    public function splitUri(?int $index = null): array|string|null
    {
        $documentRoot = $this->slash($_SERVER['DOCUMENT_ROOT']);
        $projectRoot = $this->slash(ROOT_DIR);
        $requestUri = explode("?", $_SERVER['REQUEST_URI']);
        $path = $requestUri[0] ?? '';
        $path = str_replace($projectRoot, '', $documentRoot . $path);
        $request = array_values(array_filter(array_map('trim', explode("/", $path))));
        return is_numeric($index) ? ($request[$index] ?? null) : $request;
    }

    /**
     * Generate a one-time security token.
     *
     * @param string $input The secret input used to generate the token. Defaults to '1' if not provided.
     * @param string|null $token The token to verify. If not provided, a new token is generated.
     * @return string|bool If no token is provided, returns a one-time security token. If a token is provided, returns a `boolean` indicating whether the token is valid.
     */
    public function nonce($input = '1', ?string $receivedNonce = null): string|bool
    {
        $secretKey = UssEnum::SECRET_KEY . md5($_SESSION['USSID']);
        $algorithm = 'ripemd160';
        $salt = bin2hex(random_bytes(3));
        $dataToHash = $input . $salt . $secretKey;

        $nonce = hash_hmac($algorithm, $dataToHash, $secretKey);

        if ($receivedNonce === null) {
            return $nonce . ':' . $salt;
        } else {
            $token = explode(':', $receivedNonce);
            if(count($token) === 2) {
                list($expectedNonce, $expectedSalt) = $token;
                $computedNonce = hash_hmac($algorithm, $input . $expectedSalt . $secretKey, $secretKey);
                return hash_equals($computedNonce, $expectedNonce);
            } else {
                return false;
            }
        }

    }

};
