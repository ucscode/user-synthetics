<?php
/**
 * The central class for managing User Synthetics application
 *
 * User Synthetics is a web development system or framework designed to facilitate the efficient and effective building of professional web applications. It aims to streamline the development process by combining the flexibility of PHP programming language with pre-built components and extensive library integration.
 *
 * > User Synthetics requires PHP version 7.4 or higher due to its reliance on typed properties, which are essential for maintaining the integrity of relevant properties and preventing structure changes.
 *
 * @package uss
 * @author ucscode
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
     * @param UssTwigBlockManager $ussTwigBlockManager: A Block manager that enables you write or remove content from template blocks without editing the template file
     *
     * @return void
     */
    public function render(string $templateFile, array $variables = [], ?UssTwigBlockManager $blockManager = null): void
    {
        if(!$this->rendered) {

            # Make namespace case insensitive;
            $templateFile = $this->refactorNamespace($templateFile);

            $variables = array_merge($variables);

            $twig = new \Twig\Environment($this->twigLoader, ['debug' => true]);
            $twig->addExtension(new \Twig\Extension\DebugExtension());

            $extension = $this->localTwigExtension($blockManager ?? UssTwigBlockManager::instance())->init();
            $twig->addGlobal('Uss', $extension);

            /**
            * To add twig extension from a module, create a class that implement the '\Twig\Extension\ExtensionInterface'
            * It will automatically be added to twig extension. E.G
            * class MyTwigExtension extends \Twig\Extension\AbstractExtension {}
            * The '\Twig\Extension\AbstractExtension' already implements the '\Twig\Extension\ExtensionInterface'
            */
            foreach(get_declared_classes() as $class) {
                $reflection = new ReflectionClass($class);
                if($reflection->implementsInterface('\\Twig\\Extension\\ExtensionInterface')) {
                    $isModular = preg_match("#^" . UssEnum::MOD_DIR . "#i", $reflection->getFileName());
                    if(!$twig->hasExtension($class) && $reflection->isInstantiable() && $isModular) {
                        $twig->addExtension(new $class());
                    };
                };
            };

            $this->rendered = print($twig->render($templateFile, $variables));
        }
    }

    /**
     * Set the focus on a specific URL path and execute a function based on the URL match.
     */
    public function route(string $route, callable $controller, $methods = null): bool|object
    {
        $router = new class ($route, $controller, $methods) {
            protected $request;
            private array|bool $authentic = [];
            private $requestMatch;
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
                $this->request = Uss::instance()->filterContext(Uss::instance()->query());

                # Compare the request path to the current URL
                $this->authentic[] = !!preg_match('~^' . $route . '$~i', $this->request, $this->requestMatch);

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

        $this->routes[] = $router;

        if($router->authentic) {

            // Execute the controller and pass the matching request as argument
            call_user_func($router->controller, $router->requestMatch);

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
    public function query(?int $index = null)
    {
        $documentRoot = Core::rslash($_SERVER['DOCUMENT_ROOT']);
        $projectRoot = Core::rslash(ROOT_DIR);
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
    public function nonce($input = '1', ?string $receivedNonce = null)
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

    /**
     * Kill the script and print a JSON response.
     * @return void
     */
    public function die(?bool $status = null, ?string $message = null, ?array $data = [])
    {
        parent::exit($status, $message, $data);
    }

    /**
     * Pass a variable from PHP to JavaScript.
     *
     * The `Uss::instance()->console()` method facilitates the transfer of data from PHP to JavaScript in a convenient manner.
     * It provides different functionalities based on the arguments passed:
     *
     * - If the first argument is `NULL`, it returns an array containing the list of data that will be forwarded to the browser.
     * - If the first argument is a string and the second argument is not supplied, it returns the value associated with the string key.
     * - If both the first and second arguments are supplied, it saves the value and prepares to forward it to the browser.
     *
     * > Avoid passing sensitive information to the console, as it can be easily accessed on the client browser.
     *
     * @param string|null $key  The key or identifier for the data to be passed.
     * @param mixed $value The value to be associated with the given key.
     *
     * @return mixed If no key is specified, an array of data to be forwarded to the browser. If a key is provided, the associated value is returned.
     */
    public function console(?string $key = null, mixed $value = null)
    {
        // accepts 2 arguments
        if(is_null($key)) {
            return $this->console;
        }
        $key = trim($key);
        $args = func_get_args();
        if(func_num_args() === 1) {
            return $this->console[$key] ?? null;
        }
        $this->console[$key] = $value;
    }


    /**
     * Remove a value from the list of console data.
     *
     * The `Uss::instance()->remove_console()` method allows you to remove a specific value from the console data list.
     *
     * @param string $key The key or identifier of the value to be removed from the console data.
     *
     * @return mixed The value that was removed, or `null` if the key does not exist.
     */
    public function remove_console(string $key)
    {
        if(isset($this->console[$key])) {
            $value = $this->console[ $key ];
            unset($this->console[ $key ]);
            return $value;
        }
    }



};


/** Instantiate The Uss Class */
Uss::instance();