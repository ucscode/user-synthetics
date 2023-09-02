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

class Uss
{   
    /** To instantiate an object in all global space **/

    use SingletonTrait;

    /** @ignore */
    private $project_url = 'https://github.com/ucscode/user-synthetics';

    /**
     * Global storage container for User Synthetics application.
     *
     * This property holds an array that serves as a global storage container for the User Synthetics application.
     * It allows developers to store and access data across different parts of the platform.
     *
     * @var array
     */
    public static array $global = [];


    /**
     * The console property is used to store data that is passed from the PHP environment to the JavaScript environment.
     * It holds key-value pairs of console messages and data for communication between the two environments.
     *
     * @var array
     * @ignore
     */
    private array $console = [];

    /**
     * A container for preserving focused expressions
     *
     * This property stores information about the focused URLs in the User Synthetics application.
     *
     * It is an array that holds details such as the focused expression (regexp) and the callable function associated with the focus.
     *
     * @var array
     * @ignore
     */
    private array $routes = [];

    /**
     * The viewing property indicates whether the User Synthetics application is currently in a viewing state.
     *
     * It is a boolean value that determines if the application is actively rendering content for display.
     *
     * @var bool
     * @ignore
     */
    private bool $viewing = false;

    /**
     * The engineTags property is used to store tags that are dynamically generated and used within the User Synthetics engine.
     * These tags can be used for various purposes, such as replacing placeholders in templates or storing additional information.
     *
     * @var array
     * @ignore
     */
    private array $engineTags = [];

    /** @ignore **/
    private bool $rendered = false;
    private $twigLoader;
    private $defaultTwigNamespace;

    /**
     * Initializes the User Synthetics application.
     * This method is responsible for performing essential initialization tasks, such as connecting to the database,
     * setting up session variables, and defining global variables.
     *
     * @return void
     * @ignore
     */
    protected function __construct()
    {
        define('EVENT_ID', "_");
        define('CONFIG_DIR', CORE_DIR . "/config");

        $this->twigLoader = new \Twig\Loader\FilesystemLoader();
        $this->defaultTwigNamespace = basename(__CLASS__);
        $this->twigLoader->addPath(VIEW_DIR, $this->defaultTwigNamespace);
        $this->twigLoader->addPath(VIEW_DIR, '__main__');

        require_once CONFIG_DIR . "/database.php";
        require_once CONFIG_DIR . "/variables.php";
        require_once CONFIG_DIR . "/session.php";

        $this->importTwigAssets();
    }

   

    /**
     * Register a template directory with in Uss
     *
     * Modules that intend to use twig template must specify a unique namespace and
     * the directory that contains their twig template. Then they can render their template
     * or work with template from other modules using the syntax
     * ```php
     *  Uss::instance()->render('@namespace/file.html.twig', []);
     * ```
     */
    public function addTwigFilesystem(string $namespace, string $directory)
    {
        # Prepare Namespaces
        $systemBase = strtolower($this->defaultTwigNamespace);
        $namespace = strtolower($namespace);

        # Start Comparism
        if(!preg_match("/\w+/i", $namespace)) {
            throw new Exception(__METHOD__ . " #(Argument 1); Twig namespace may only contain letter, numbers and underscore");
        } elseif($namespace === $systemBase) {
            throw new Exception(__METHOD__ . " #(Argument 1); Use of `{$namespace}` as namespace is not allowed");
        };

        # Check Uniqueness
        $namespace = ucfirst($namespace);
        if(in_array($namespace, $this->twigLoader->getNamespaces())) {
            throw new Exception(__METHOD__ . " #(Argument 1); `{$namespace}` namespace already exists");
        };

        # Add Namespace and Directory;
        $this->twigLoader->addPath($directory, $namespace);
    }

    /**
     * Render A Twig Template
     */
    public function render(string $templateFile, array $variables = [], ?UssTwigBlockManager $ussTwigBlockManager = null)
    {
        # Prevent Multiple Rendering
        if($this->rendered) {
            return;
        };
        
        # Make namespace case insensitive;
        if(substr($templateFile, 0, 1) === '@') {
            $split = explode("/", $templateFile);
            $split[0] = strtolower($split[0]);
            $split[0][1] = strtoupper($split[0][1]);
            $templateFile = implode("/", $split);
        };
        
        # Update Variables
        $variables = array_merge($variables);

        # Load Twig
        $twig = new \Twig\Environment($this->twigLoader, [
            'debug' => true
        ]);

        # Add Extension
        $twig->addExtension(new \Twig\Extension\DebugExtension());

        if( $ussTwigBlockManager === null ) {
            $ussTwigBlockManager = UssTwigBlockManager::instance();
        };

        # Custom Extension;
        $twig->addGlobal('Uss', require_once CONFIG_DIR . "/UssAnonymousTwigExtension.php");

        # Render Template
        echo $twig->render($templateFile, $variables);

        $this->rendered = true;

    }

    /**
     * Display content on the browser using a fully featured header and footer.
     *
     * This method is used to render content on the browser by providing a callable which represents the content of the view template. It provides a visually blank page with a fully featured header and footer, including assets such as Bootstrap, SEO tags, and other resources.
     * By using this method, you are creating a blank page with a doctype declaration, ensuring the availability of necessary resources.
     *
     * @param callable|null $content Optional: A callable that represents the content of the view template.
     * @return null|bool Returns `null` if the content is supplied. Otherwise, returns a `boolean` indicating if content has already been displayed.
     */
    public function view(?callable $content = null, ?array $exclib = [], ?array $inclib = [])
    {
        if(is_null($content) || $this->viewing) {
            return $this->viewing;
        }


        /**
         * Output Buffering
         *
         * In order to activate user synthetic's template engine, output buffering needs to be turned on
         * Hence, no output will be sent to the browser.
         * Instead, the output will be captured &amp; stored in an internal buffer
         */

        $level = ob_get_level();

        ob_start();

        /**
         * Now one problem about output buffer is
         * When an error or an uncaught exception is thrown, the script is exited
         * The printed error message then dis-organizes the page
         *
         * We need to print exception on a top level buffer (A completely blank page)
         * Just like a normal PHP script would do
         */

        try {


            # OUTPUT THE HEADER!

            require VIEW_DIR . '/header.php';


            # EXECUTE THE CALLABLE CONTENT

            call_user_func($content);


            # OUTPUT THE FOOTER!

            require VIEW_DIR . '/footer.php';


        } catch (Exception $ex) {

            // discard all buffer output and focus only on the exception

            while (ob_get_level()) {
                ob_end_clean();
            }

            throw $ex;

        }

        /**
         * Capture the buffered content
         *
         * Copy the content of the internal buffer into a string
         * Then, replace every eTag in the string
         *
         */

        $output = Core::replace_var(ob_get_clean(), $this->engineTags);


        # OUTPUT THE CONTENT!

        print_r($output);


        /**
         *
         */

        # CHANGE THE VIEW STATUS;

        $this->viewing = true;

    }


    /**
     * Set the focus on a specific URL path and execute a function based on the URL match.
     *
     * This method allows you to create a focus expression, which executes a function only if the URL matches a particular regular expression path. This is a powerful mechanism recommended to control the execution of your code based on specific URL patterns and preventing it from running globally and respecting other modules.
     *
     * Additionally, you can specify the request method on which the function should be called using the `$request` parameter. By default, the function will be called for `GET` requests. You can set `$request` to `'POST'` if you want the function to be called only for `POST` requests, or set it to `null` to allow the function to be called for both `GET` and `POST` requests.
     *
     * @page Get Focus Method
     * @param string $path The regular expression path to match against the URL
     * @param callable $controller The function to be called if the URL matches the expression
     * @param string|null $request The request method on which the function should be called ('GET', 'POST', or `null`)
     * @return null
     */
    public function route(string $path, callable $controller, $methods = null)
    {
        $router = new class ($path, $controller, $methods) {
            # public properties
            public $controller;

            protected $request;
            protected $route;
            protected $methods;

            private $authentic = [];
            private $requestMatch;
            private $backtrace;

            public function __construct($path, $controller, $methods)
            {
                $this->route = $path;
                $this->controller = $controller;
                $this->methods = $methods;
                $this->configure();
            }

            public function __get($key)
            {
                return $this->{$key} ?? null;
            }

            private function configure()
            {
                $this->filterMethods();
                $this->resolveRoute();
                $this->authentic = !in_array(false, $this->authentic);
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

            /**
            * Uss::instance()->route( "users/profile", function() {
            * 	This closure will work only if domain name is directly followed by `users/profile`
            * 	# domain.com/users/profile = true
            * 	# domain.com/user/profile = false
            * 	# domain.com/users/profile2 = false
            * });
            */
            protected function resolveRoute()
            {
                $route = implode(
                    "/",
                    array_filter(
                        array_map(
                            'trim',
                            explode("/", $this->route)
                        )
                    )
                );

                # The request
                $this->request = implode("/", Uss::instance()->query());

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
                    } elseif(($currentTrace['class'] ?? null) == Uss::instance()::class) {
                        if(strtolower($currentTrace['function']) == 'route') {
                            $this->backtrace = $currentTrace;
                        };
                    };
                };
            }

        };

        # The @route.routed event can be used to modify any controller output
        Events::instance()->exec('@route.routed', ['router' => $router]);

        if($router->authentic) {
            # Execute the controller
            call_user_func($router->controller, $router->requestMatch);
        };

        $this->routes[] = $router;

        return $router->authentic ? $router : false;

    }

    /**
     * Get the current focus expression or list of focus expressions.
     *
     * This method retrieves the current focus expression that has been set using the `Uss::instance()->route()` method. The focus expression represents the URL path pattern on which a specific function is executed. If the `$expr` parameter is set to `true`, an array of all focus expressions and their corresponding URLs will be returned. Otherwise, if the `$expr` parameter is `false` or not provided, only the current focus expression will be returned.
     *
     * @param bool $expr Optional: Whether to return the list of focus expressions or just the current focus expression. Default is `false`.
     * @return string|array|null The current focus expression, an array of focus expressions and their corresponding URLs, or `null` if no focus expressions are set
    */
    public function getRouteInventory(bool $authentic = false)
    {
        $routes = $this->routes;
        if($authentic) {
            $routes = array_filter($routes, function ($route) {
                return $route->authentic;
            });
        };
        return $routes;
    }


    /**
     * Retrieve URL request path segments.
     *
     * This method splits the URL request string into individual segments and returns them as an array. The method also accepts an optional integer argument that specifies the index of the segment to retrieve or extract. If the index is out of range, it returns `null`.
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
     * The `Uss::instance()->nonce()` method generates a one-time security token based on a secret input and the current session ID. This token can be used for secure operations, such as verifying the authenticity of requests. To verify a token, simply provide it as the second argument when invoking the method.
     *
     * @param string $input The secret input used to generate the token. Defaults to '1' if not provided.
     * @param string|null $token The token to verify. If not provided, a new token is generated.
     * @return string|bool If no token is provided, returns a one-time security token. If a token is provided, returns a `boolean` indicating whether the token is valid.
     */
    public function nonce($input = '1', ?string $token = null)
    {

        // generate a new session_id;

        $hash = call_user_func(function () use ($input) {

            // get length of uss_session_id
            $length = strlen($_SESSION['uss_session_id']);

            // join hashed input with hashed session_id;
            $bind_hash = hash('sha256', session_id()) . hash('sha256', $input);

            // extract some string and split the string into array;
            $input =  str_split(substr($bind_hash, -$length), 5);

            // encode the uss_session_id and split the string into array;
            $session_id = str_split(str_rot13($_SESSION['uss_session_id']), 5);

            $result = [];

            // now! rearrange the strings into a very improper and abnormal way
            for($x = 0; $x < count($session_id); $x++) {
                $__a = str_rot13($input[ $x ] ?? '');
                $__b = str_rot13($session_id[ $x ] ?? '');
                $result[] = $__a . $__b;
            };

            // join the improper string
            return implode('', $result);

        });

        // return a hashed version of the improper string!

        if(is_null($token)) {
            return password_hash($hash, PASSWORD_BCRYPT);
        }

        return password_verify($hash, $token);

    }


    /*
     * I Love JSO...
     * I mean, uss platform works great with JSON!
     *
     * `Uss::instance()->exit` method is the platform way of calling `die()` or `exit()`
     * It exits the script and print a json response
     */

    /**
     * Exit the script and print a JSON response.
     *
     * The `Uss::instance()->exit()` method is used to terminate the script execution and return a JSON response. This method is particularly useful when handling AJAX requests and returning structured data.
     *
     * @param bool|null   $status  The status of the response. Set to `true` for a successful response, or `false` for an error response.
     * @param string|null $message A message accompanying the response. It can provide additional information about the status or error.
     * @param array|null  $data    Additional data to include in the response. It should be an associative array.
     *
     * @return void
     */
    public function exit(?string $message = null, ?bool $status = null, ?array $data = [])
    {
        $args = func_get_args();
        if(empty($args)) {
            $output = '';
        } elseif(count($args) === 1) {
            $output = $message;
        } else {
            $output = json_encode([
                "message" => $message,
                "status" => (bool)$status,
                "data" => $data
            ]);
        };
        exit($output);
    }

    public function die(?bool $status = null, ?string $message = null, ?array $data = [])
    {
        $this->exit($status, $message, $data);
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
    public function console(?string $key = null)
    {
        // accepts 2 arguments
        if(is_null($key)) {
            return $this->console;
        }
        $key = trim($key);
        $args = func_get_args();
        if(count($args) === 1) {
            return $this->console[ $key ] ?? null;
        }
        $this->console[ $key ] = $args[1];
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

    /**
     * Assign and update template tag values in user synthetics.
     *
     * The `Uss::instance()->tag()` method is used in the User Synthetics framework to modify content through template tags. Template tags are written in the format `%\{tagName}` and can be replaced with corresponding values.
     *
     * When encountering a tag, the method checks the `engineTags` list to find a matching key. If a match is found, the tag is replaced with the corresponding string value. Otherwise, the tag is replaced with an empty string.
     *
     * @param string|null $key The tag name to be replaced. If set to null, an array containing a list of all tags will be returned and the other parameters will be ignored.
     * @param string|null $value The value to assign or update the tag. If set to null, the tag will be removed from the tag list. If not supplied, the value of the tag will be returned. If a string value is provided, the tag value will be assigned or updated.
     * @param bool $overwrite (default = `true`) Set to `false` if the value of an existing tag should not be overwritten.
     *
     * @return string|null If $key is set to `null`, an array containing a list of all tags. Otherwise, returns the value of the specified tag or `null` if the tag doesn't exist.
     */
    public function tag(?string $key, ?string $value = null, bool $overwrite = true)
    {

        if(is_null($key)) {
            return $this->engineTags;
        }

        if(!array_key_exists(1, func_get_args())) {
            return ($this->engineTags[ $key ] ?? null);
        }

        // Try not to overwrite an existing tag;
        if(!$overwrite && array_key_exists($key, $this->engineTags)) {
            return;
        }

        if(is_null($value)) {
            // remove an existing tag
            if(array_key_exists($key, $this->engineTags)) {
                unset($this->engineTags[$key]);
            };
            return;
        };

        // Assign a new tag
        $this->engineTags[$key] = $value;
    }

    /**
    * @ignore
    */
    private function importTwigAssets()
    {
        $ussTwigBlockManager = UssTwigBlockManager::instance();
        
        # All CSS & JS are retrieved from the ASSET_DIR

        $libs = [
            'head_css' => [
                'bootstrap' => 'css/bootstrap.min.css',
                'bs-icon' => 'vendor/bootstrap-icons/bootstrap-icons.css',
                'animate' => 'css/animate.min.css',
                'glightbox' => "vendor/glightbox/glightbox.min.css",
                'toastr' => 'vendor/toastr/toastr.min.css',
                'font-size' => "css/font-size.min.css",
                'main-css' => 'css/main.css'
            ],
            'body_js' => [
                'jquery' => 'js/jquery-3.6.4.min.js',
                'bootstrap' => 'js/bootstrap.bundle.min.js',
                'bootbox' => 'js/bootbox.all.min.js',
                'glightbox' => "vendor/glightbox/glightbox.min.js",
                'toastr' => 'vendor/toastr/toastr.min.js',
                'notiflix-loading' => 'vendor/notiflix/notiflix-loading-aio-3.2.6.min.js',
                'notiflix-block' => 'vendor/notiflix/notiflix-block-aio-3.2.6.min.js',
                'main-js' => 'js/main.js'
            ]
        ];

        foreach( $libs as $block => $contents ) {

            $contents = array_map(function($value) {
                $type = explode(".", $value);
                $type = strtoupper(end($type));
                $value = Core::url(ASSETS_DIR . "/" . $value);
                if( $type == 'CSS' ) {
                    $element = "<link rel='stylesheet' href='" . $value . "'>";
                } else {
                    $element = "<script type='text/javascript' src='" . $value . "'></script>";
                };
                return $element;
            }, $contents);

            $ussTwigBlockManager->appendTo($block, $contents);
            
        };

    }

};


/** Instantiate The Uss Class */
Uss::instance();
