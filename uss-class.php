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
 * @version 2.5.0
 */

class uss {
	
	const VERSION = "2.5.0";
	
	/**
	 * @ignore
	 */
	private static $project_url = 'https://github.com/ucscode/user-synthetics';
	
	
	/**
	 * Global storage container for User Synthetics application.
	 *
	 * This property holds an array that serves as a global storage container for the User Synthetics application.
	 * It allows developers to store and access data across different parts of the platform.
	 *
	 * @var array
	 */

	public static array $global = array();
	
	
	/**
	 * The console property is used to store data that is passed from the PHP environment to the JavaScript environment.
	 * It holds key-value pairs of console messages and data for communication between the two environments.
	 *
	 * @var array
	 * @ignore
	 */
	private static array $console = array();
	
	
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
	private static array $focusURLs = array();
	
	
	/**
	 * The viewing property indicates whether the User Synthetics application is currently in a viewing state.
	 *
	 * It is a boolean value that determines if the application is actively rendering content for display.
	 *
	 * @var bool
	 * @ignore
	 */
	private static bool $viewing = false;
	
	
	/**
	 * The init property represents the initialization state of the User Synthetics application.
	 * It is a boolean value that indicates whether the application has been initialized.
	 *
	 * @var bool
	 * @ignore
	 */
	private static bool $init = false;
	
	
	/**
	 * The engineTags property is used to store tags that are dynamically generated and used within the User Synthetics engine.
	 * These tags can be used for various purposes, such as replacing placeholders in templates or storing additional information.
	 *
	 * @var array
	 * @ignore
	 */
	private static array $engineTags = array();
	
	
	/**
	 * Initializes the User Synthetics application.
	 * This method is responsible for performing essential initialization tasks, such as connecting to the database,
	 * setting up session variables, and defining global variables.
	 *
	 * @return void
	 * @ignore
	 */
	public static function __init() {
		
		// If the USS has been initialized, ignore the call to this method;
		
		if( self::$init ) return;
		
		/**
		 * User Synthetics is primarily built upon events, and the priority of an event is determined by its event ID.
		 * This docblock provides insights into the event priority system and guidelines for module developers.
		 *
		 * ## Event Priority Usage
		 *
		 * User Synthetics has chosen `_` as its default event ID.
		 * However, to ensure proper execution order and prevent conflicts, follow these guidelines:
		 *
		 * - Modules that need to execute **before** any default USS events should use event IDs starting with "Negative Numbers" or "Upper Case" Characters.
		 * - Modules that need to execute **after** the default USS events should use event IDs starting with "Positive Numbers" or "Lower Case" Characters.
		 * - Modules that want to **override** the default USS event should use the same event ID as that of USS.
		 *
		 * By following these guidelines, modules can effectively prepend, append, or overwrite USS default events to customize the behavior of User Synthetics.
		 *
		 * @param string $eventID The event ID indicating the priority of the event.
		 * @return void
		 */
		define( 'EVENT_ID', "_" );
		
		
		/** 
		 * Prepare the essential variables required for the formation of user synthetics;
		 * These variables may be overridden by other modules
		 */
		self::__vars();
		
		
		/**
		 * Establishes a database connection.
		 * The connection file `conn.php` is used to establish the database connection.
		 * If the `DB_CONNECT` constant is set to `false`, the database connection will be ignored.
		 */
		self::__connect();
		
		
		/**
		 * Initializes the session.
		 * Sessions are highly significant in PHP and provide important functionality. 
		 * Although cookies are great, PHP sessions offer additional capabilities.
		 * Don't like sessions? Then you should consider deleting User Synthetics (just kidding!).
		 */
		self::__session();
		
		
		/**
		 * Marks the end of the initialization process.
		 * This signifies the completion of the initialization phase.
		 */
		self::$init = true;
		
	}
	
	
	/*
	 * PRIVATE: [Methods below are not accessible]
	 *
	 * This comment indicates that the methods listed below are private and not intended for external access.
	 *
	 * @category Private
	 * @access private
	 */
	
	
	/**
	 * Connect to the database.
	 *
	 * This method establishes a connection to the database using the credentials defined in the `conn.php` file.
	 * If the `DB_CONNECT` constant is set to `false`, the database connection will be ignored.
	 *
	 * @category Database
	 * @access private
	 *
	 * @return void
	 * @ignore
	 */
	private static function __connect() {
		
		# Now! Let's setup DataBase Connection!

		if( DB_CONNECT ) {
			
			/**
			 * Save MYSQLI Instance into `uss::$global` property.
			 *
			 * This proccess assigns the MYSQLI instance to the `uss::$global` property, allowing easy access to the database connection throughout the User Synthetics system.
			 *
			 * Please note that if you are using **PHP Data Object** (PDO) connection instead of MYSQLI, you will need to declare your own connection inside your module using the appropriate syntax.
			 *
			 * Example for PDO connection:
			 *
			 * ```php
			 * uss::$global['pdo'] = new PDO('mysql:host=localhost;dbname=test', $user, $pass);
			 * ```
			 */
			
			try {
				
				self::$global['mysqli'] = @new mysqli( DB_HOST, DB_USER, DB_PASSWORD, DB_NAME );
				
				// if database connection fails;
				
				if( self::$global['mysqli']->connect_errno ) {
					
					/**
					 * Display error message;
					 * Applicable to PHP 7.4
					 */
					
					self::eTag('connect_error', self::$global['mysqli']->connect_error);
					
					self::view(function() {
						require_once VIEW_DIR . '/db-conn-failed.php';
					});
					
					exit;
					
				} else {
					
					/** 
					 * Create Database Option Table.
					 *
					 * The option table allows storing various settings and configurations that can be accessed and modified easily.
					 * It provides a centralized location to manage site-specific options and preferences.
					 */
					
					$__options = new pairs( self::$global['mysqli'], DB_TABLE_PREFIX . "_options" );
					
					self::$global['options'] = $__options;
					
				};
			
			} catch(Exception $e) {
				
				/**
				 * Catch "mysqli_sql_exception"
				 * Applicable to PHP 8
				 */
				
				self::eTag('connect_error', $e->getMessage());
				
				self::view(function() {
					require_once VIEW_DIR . "/db-conn-failed.php";
				});
				
				exit;
				
			}
			
			/**
			 * Uhhh... :\
			 * You seem to have turned off the database connection!
			*/
			
		} else self::$global['mysqli'] = self::$global['options'] = null;
		
	}
	
	
	/**
	 * Establish a new session, create a session ID, and generate a browser unique ID.
	 *
	 * This method is responsible for initializing a new session in the User Synthetics system.
	 *
	 * It creates a session ID to identify the session and generates a unique ID specific to the user's browser.
	 *
	 * However, please note that relying solely on the browser ID for long-term usage is not recommended as clearing browser cookies can easily wipe out the browser ID and generate a new one.
	 *
	 * @return void
	 * @ignore
	 */
	private static function __session() {
		
		# Let's start the session!
		
		if( empty(session_id()) ) session_start();
		
		
		/* Create a unique visitor session ID */
		
		if( empty($_SESSION['uss_session_id']) || strlen($_SESSION['uss_session_id']) < 50 ) {
			$_SESSION['uss_session_id'] = core::keygen(mt_rand(50, 80), true);
		};
		
		
		/* - Unique Device ID; */
		
		if( empty($_COOKIE['ussid']) ) {
			$time = (new DateTime())->add( (new DateInterval("P6M")) );
			$_COOKIE['ussid'] = uniqid(core::keygen(7));
			setrawcookie( 'ussid', $_COOKIE['ussid'], $time->getTimestamp(), '/' );
		};
		
		
	}
	
	
	/**
	 * Set up and manage system-defined variables.
	 *
	 * This method is responsible for handling system-defined variables
	 * The method stores values which can be accessed across different parts
	 * of the system through the `uss::$global` variable.
	 *
	 * @return void
	 * @ignore
	 */
	private static function __vars() {
		
		/**
		 * Get Default Content
		 * All of the settings below can be easily modified by any module
		*/
		
		self::$global['icon'] = core::url( ASSETS_DIR . '/images/origin.png' );
		self::$global['title'] = PROJECT_NAME;
		self::$global['tagline'] = "The excellent development tool for future oriented programmers";
		self::$global['copyright'] = ((new DateTime())->format('Y'));
		self::$global['description'] = "Ever thought of how to make your programming life easier in developing website? \nUser Synthetics offers the best solution for a simple programming lifestyle";
		self::$global['website'] = self::$project_url;
			
		/**
		 * Set body attributes for the HTML output.
		 *
		 * This method allows you to add attributes to the `<body>` tag
		 * of the rendered HTML output. You can provide an associative array
		 * of attribute key-value pairs to be included in the `<body>` tag.
		 * The attributes will be rendered as part of the opening `<body>` tag.
		 *
		 * Example usage:
		 *
		 * ```php
		 * uss::$global['body.attrs'] = [
		 *     'data-name' => 'ucscode',
		 *     'style' => 'color:green;background:red',
		 *     'class' => 'uss-v2'
		 * ];
		 * ```
		 *
		 * When the HTML output is rendered, the `<body>` tag will contain
		 * the specified attributes:
		 *
		 * ```html
		 * <body data-name="ucscode" style="color:green;background:red" class="uss-v2">
		 *     ...
		 * </body>
		 * ```
		 */
		self::$global['body.attrs'] = array("class" => 'uss');
		
	}
	
	/**
	 * Public Methods:
	 * The methods listed below are accessible and can be called from external code.
	 */
	
	/**
	 * Display content on the browser using a fully featured header and footer.
	 *
	 * This method is used to render content on the browser by providing a callable which represents the content of the view template. It provides a visually blank page with a fully featured header and footer, including assets such as Bootstrap, SEO tags, and other resources.
	 * By using this method, you are creating a blank page with a doctype declaration, ensuring the availability of necessary resources.
	 *
	 * @param callable|null $content Optional: A callable that represents the content of the view template.
	 *
	 * @return null|bool Returns `null` if the content is supplied. Otherwise, returns a `boolean` indicating if content has already been displayed.
	 */
	public static function view( ?callable $content = null ) {
		
		/**
		 * Check the view status before printing to prevent duplicate rendering.
		 * The `uss::view()` method will not reprint the user interface onto the browser if it has already been rendered.
		 */
		
		if( is_null($content) || self::$viewing ) return self::$viewing;
		
		
		/**
		 * save platform name to javascript!
		 * accessible throught `uss.platform`
		 */
		self::console( 'Platform', PROJECT_NAME );
		
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
			
			while (ob_get_level()) ob_end_clean();
			
			throw $ex;
			
		}
			
		/**
		 * Capture the buffered content
		 *
		 * Copy the content of the internal buffer into a string
		 * Then, replace every eTag in the string
		 *
		 */
		
		$output = core::replace_var( ob_get_clean(), self::$engineTags );
		
		
		# OUTPUT THE CONTENT!
		
		print_r( $output );
		
		
		/**
		 * 
		 */
		
		# CHANGE THE VIEW STATUS;
		
		self::$viewing = true;
		
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
	 * @param callable $func The function to be called if the URL matches the expression
	 * @param string|null $request The request method on which the function should be called ('GET', 'POST', or `null`)
	 * @return null
	 */
	public static function focus( string $path, callable $func, ?string $request = "get" ) {
		
		if( !is_null($request) && $_SERVER['REQUEST_METHOD'] !== strtoupper($request) ) return;
		
		/**
		 * Further Example:
		 * ```php
		 * uss::focus( "users/profile", function() {
		 * 	/* 
		 * 		This closure will work only if domain name is directly followed by `users/profile` 
		 * 		# domain.com/users/profile - [ will work ]
		 * 		# domain.com/user/profile - [ will not work ]
		 * 		# domain.com/users/profile2 - [ will not work ]
		 *  {@*}
		 * });
		 *```	
		*/
		$focus = implode("/", array_filter(array_map('trim', explode("/", $path))));
		$query = implode("/", self::query());
		
		/**
		 * Check if they match;
		 * Test for regular expression of the Path
		*/
		$expression = '~^' . $focus . '$~';

		/**
		 * Compare the focus path to the current URL
		 */
		if( preg_match( $expression, $query, $match ) ) {
		
			/**
			 * If the expression matches,
			 * Save the focus string into the @var focusURLs.
			 * *Useful for backtracing and debugging*
			*/
			
			$key = array_search( __FUNCTION__, array_column( debug_backtrace(), 'function' ) );
				
			$trace = debug_backtrace()[ $key ];
			
			self::$focusURLs[] = array(
				"focus" => $focus,
				"file" => $trace['file'],
				"line" => $trace['line'],
				"callable" => &$func
			);

			/**
			 * The :{focused} event can be used to trap a focus to modify the output
			 */
			events::exec(':{focused}', end(self::$focusURLs));
			
			if( empty($func) || !is_callable($func) ) return;
			
			/**
			 * Execute the callable
			 */
			call_user_func($func, $match);
		
		};
		
	}

	
	/**
	 * Get the current focus expression or list of focus expressions.
	 *
	 * This method retrieves the current focus expression that has been set using the `uss::focus()` method. The focus expression represents the URL path pattern on which a specific function is executed. If the `$expr` parameter is set to `true`, an array of all focus expressions and their corresponding URLs will be returned. Otherwise, if the `$expr` parameter is `false` or not provided, only the current focus expression will be returned.
	 *
	 * @param bool $expr Optional: Whether to return the list of focus expressions or just the current focus expression. Default is `false`.
	 * @return string|array|null The current focus expression, an array of focus expressions and their corresponding URLs, or `null` if no focus expressions are set
	*/
	public static function getFocus( bool $expr = false ) {
		if( $expr ) return self::$focusURLs;
		if( !empty(self::$focusURLs) ) {
			$focus = end(self::$focusURLs);
			return $focus['focus'];
		};
	}
	
	
	/**
	 * Retrieve URL query path segments.
	 *
	 * This method splits the URL query string into individual segments and returns them as an array. The method also accepts an optional integer argument that specifies the index of the segment to retrieve or extract. If the index is out of range, it returns `null`.
	 *
	 * @param int|null $index Optional: index of the segment to retrieve. If not provided, returns the entire array of segments.
	 * @return array|string|null The array of URL path segments if no index is provided, the segment at the specified index, or `null` if the index is out of range or the query string is not set.
	 */
	public static function query( ?int $index = null ) {
		
		$DOCUMENT_ROOT = core::rslash($_SERVER['DOCUMENT_ROOT']);
		$PROJECT_ROOT = core::rslash(ROOT_DIR);
		$REQUEST_URI = $_SERVER['REQUEST_URI'];
		
		$PATH = str_replace($PROJECT_ROOT, '', $DOCUMENT_ROOT . $REQUEST_URI);
		$QUERY = array_values( array_filter( array_map('trim', explode("/", $PATH)) ) );
		
		return is_numeric($index) ? ($QUERY[$index] ?? null) : $QUERY;
		
	}
	
	
	/**
	 * Generate a one-time security token.
	 *
	 * The `uss::nonce()` method generates a one-time security token based on a secret input and the current session ID. This token can be used for secure operations, such as verifying the authenticity of requests. To verify a token, simply provide it as the second argument when invoking the method.
	 *
	 * @param string $input The secret input used to generate the token. Defaults to '1' if not provided.
	 * @param string|null $token The token to verify. If not provided, a new token is generated.
	 * @return string|bool If no token is provided, returns a one-time security token. If a token is provided, returns a `boolean` indicating whether the token is valid.
	 */
	public static function nonce( $input = '1', ?string $token = null ) {
		
		// generate a new session_id;
		
		$hash = call_user_func(function() use($input) {
			
			// get length of uss_session_id
			$length = strlen($_SESSION['uss_session_id']);
			
			// join hashed input with hashed session_id;
			$bind_hash = hash('sha256', session_id()) . hash('sha256', $input);
			
			// extract some string and split the string into array;
			$input =  str_split( substr($bind_hash , -$length), 5 );
			
			// encode the uss_session_id and split the string into array;
			$session_id = str_split( str_rot13( $_SESSION['uss_session_id'] ), 5 );
			
			$result = [];
			
			// now! rearrange the strings into a very improper and abnormal way
			for( $x = 0; $x < count($session_id); $x++ ) {
				$__a = str_rot13( $input[ $x ] ?? '' );
				$__b = str_rot13( $session_id[ $x ] ?? '' );
				$result[] = $__a . $__b;
			};
			
			// join the improper string
			return implode('', $result);
			
		});
		
		// return a hashed version of the improper string!
		
		if( is_null($token) ) return password_hash($hash, PASSWORD_BCRYPT);
		
		return password_verify( $hash, $token );
		
	}
	
	
	/*
	 * I Love JSO... 
	 * I mean, uss platform works great with JSON!
	 * 
	 * `uss::stop()` method is the platform way of calling `die()` or `exit()`
	 * It exits the script and print a json response
	 */
	
	/**
	 * Exit the script and print a JSON response.
	 *
	 * The `uss::stop()` method is used to terminate the script execution and return a JSON response. This method is particularly useful when handling AJAX requests and returning structured data.
	 *
	 * @param bool|null   $status  The status of the response. Set to `true` for a successful response, or `false` for an error response.
	 * @param string|null $message A message accompanying the response. It can provide additional information about the status or error.
	 * @param array|null  $data    Additional data to include in the response. It should be an associative array.
	 *
	 * @return void
	 */
	public static function stop( ?bool $status, ?string $message = null, ?array $data = [] ) {
		$data = array(
			"status" => (boolean)$status,
			"message" => $message,
			"data" => $data
		);
		$json = json_encode($data);
		exit( $json );
	}
	
	
	/**
	 * Pass a variable from PHP to JavaScript.
	 *
	 * The `uss::console()` method facilitates the transfer of data from PHP to JavaScript in a convenient manner.
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
	public static function console( ?string $key = null ) {
		// accepts 2 arguments
		if( is_null($key) ) return self::$console;
		$key = trim($key);
		$args = func_get_args();
		if( count($args) === 1 ) return self::$console[ $key ] ?? null;
		self::$console[ $key ] = $args[1];
	}
	
	
	/**
	 * Remove a value from the list of console data.
	 *
	 * The `uss::remove_console()` method allows you to remove a specific value from the console data list.
	 *
	 * @param string $key The key or identifier of the value to be removed from the console data.
	 *
	 * @return mixed The value that was removed, or `null` if the key does not exist.
	 */
	public static function remove_console( string $key ) {
		if( isset(self::$console[$key]) ) {
			$value = self::$console[ $key ];
			unset( self::$console[ $key ] );
			return $value;
		}
	}
	
	/**
	 * Assign and update template tag values in user synthetics.
	 *
	 * The `uss::eTag()` method is used in the User Synthetics framework to modify content through template tags. Template tags are written in the format `%\\{tagName}` and can be replaced with corresponding values.
	 *
	 * When encountering a tag, the method checks the `engineTags` list to find a matching key. If a match is found, the tag is replaced with the corresponding string value. Otherwise, the tag is replaced with an empty string.
	 *
	 * @param string|null $key The tag name to be replaced. If set to null, an array containing a list of all tags will be returned and the other parameters will be ignored.
	 * @param string|null $value The value to assign or update the tag. If set to null, the tag will be removed from the tag list. If not supplied, the value of the tag will be returned. If a string value is provided, the tag value will be assigned or updated.
	 * @param bool $overwrite (default = `true`) Set to `false` if the value of an existing tag should not be overwritten.
	 *
	 * @return string|null If $key is set to `null`, an array containing a list of all tags. Otherwise, returns the value of the specified tag or `null` if the tag doesn't exist.
	 */
	public static function eTag( ?string $key, ?string $value = null, bool $overwrite = true) {
		
		if( is_null($key) ) return self::$engineTags;
		
		if( !array_key_exists(1, func_get_args()) ) return ( self::$engineTags[ $key ] ?? null );
		
		// Try not to overwrite an existing tag;
		if( !$overwrite && array_key_exists($key, self::$engineTags) ) return;
		
		if( is_null($value) ) {
			// remove an existing tag
			if( array_key_exists($key, self::$engineTags) ) {
				unset(self::$engineTags[$key]);
			};
			return;
		};
		
		// Assign a new tag
		self::$engineTags[$key] = $value;
	}
	
};


/*
	GREAT! WE'VE DONE A LOT IN THE USS CLASS
	NOW! LET'S INITIALIZE IT!
*/

uss::__init();

