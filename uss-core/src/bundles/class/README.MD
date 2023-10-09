# Events Class

#### Unleash the Power of JavaScript-Style Events in PHP

The Event Class is a lightweight library that allows developers to implement the observer design pattern in their PHP applications. It provides an easy-to-use interface for registering and triggering event listeners, allowing developers to create decoupled, extensible code.

The Event Class is a powerful tool that enables efficient event handling and execution within PHP applications, inspired by the event listener pattern commonly used in JavaScript. It simplifies the process of managing events, allowing you to add, remove, and execute events seamlessly.

## Features

-   Simple API for registering and triggering event listeners
-   Supports multiple event listeners for a single event
-   Supports priority-based event execution
-   Supports passing data to event listeners
-   Lightweight and easy to integrate into existing projects
-   Compatible with PHP 8.0 and higher

## Benefits

-   Decoupling: Using events allows developers to separate concerns and make their code more modular. By breaking down the application into smaller, more focused parts, it becomes easier to maintain and update.
    
-   Extensibility: With the use of events, developers can create code that is easier to extend. If a new feature needs to be added, it can be implemented as a new event listener, rather than modifying existing code.
    
-   Flexibility: The event class allows developers to write code that is more flexible and adaptable. By using events, the application can be configured to behave differently based on the environment or user preferences.

-   Modular Development: Promote modular development by decoupling components through event-based communication.

-   Enhanced Maintainability: Maintain and update event logic easily, promoting code maintenance and scalability.

## Usage Example:

#### Using Callable

```php
(new Event())->addListener('myEvent', function(array $data) {

    // Handle myEvent

});
```

#### Using Class

```php
(new Event())->addListener('myEvent', new class implements EventInterface {

    public function eventAction(array $data): void 
    {
        // Handle myEvent
    }

});
```

#### Dispatching Events

```php
(new Event())->emit('myEvent');
```

In the above example, two event listeners are registered for the event named `myEvent`. <br>
Later, the `dispatch` method triggers the execution of all listeners associated with the `myEvent` Event.

## Installation

To use the `Event` Class in your project, simply include the following files:
```php
require_once 'EventInterface.php';
require_once 'Event.php'
```



## Further Example:

The event class also supports priority-based event execution. This means that events can be executed in a specific order based on their priority. For example:

```php
require_once 'EventInterface.php';
require_once 'Event.php';

use Ucscode\Event\EventInterface;
use Ucscode\Event\Event;

$order = -10;

(new Event())->addListener('user.registered', function(array $data) {

    // Perform validation on the form data
    foreach($data as $key => $value) {
        // Some Action
    };

});

/**
 * This class based Event will run before the one above
 */
(new Event())->addListener('user.registered', new class implemenets EventInterface {

    private $storage;

    public function eventAction(array $data): void 
    {
        // Process the submitted form data
        $this->storage = $data;
    }

}, $order);

$data = array( /* some form data */ );

// Trigger events

(new Event())->emit('user.registered', $data);
```

In the example above, we've set an order of `-10` for second `user.registration` Event. <br>
When we trigger/dispatch these events, the second Event (which uses `EventInterface`) will be executed first, followed by the one using a `function`


## Conclusion

The PHP Event Class is a powerful tool for creating flexible, decoupled, and extensible code. By using events, developers can write more modular and maintainable code, making it easier to adapt and evolve their applications over time. Explore the Event Class and embrace the power of JavaScript-style events in your PHP environment. Simplify event management and elevate the flexibility of your applications.

I hope you find the Event Class valuable in simplifying your event handling and execution. Happy coding!

## License

This project is licensed under the MIT License. See the [LICENSE](https://opensource.org/licenses/MIT) file for more details.

## Author

Uchenna Ajah - [uche23mail@gmail.com](mailto:uche23mail@gmail.com)

