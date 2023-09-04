<?php
/**
 * A Powerful Event Management System for PHP Applications
 *
 * The Event class is an independent class that provides a mechanism for managing custom events within PHP applications.
 * This class enables developers to create,register, and trigger events, promoting code modularity, reusability, and extensibility.
 *
 * The Event class follows the observer pattern, where objects interested in receiving notifications about
 * specific events can register themselves as listeners and respond accordingly when those events occur.
 * It encapsulates the functionality related to event management, providing a straightforward and intuitive
 * API for event handling and dispatching.
 *
 * #### Example
 *
 * ```php
 * Events::instance()->addListener("user.created", function($data) {
 *     // Handle user created event
 * }, 'listener-id-1');
 *
 * Events::instance()->addListener("user.created", function($data) {
 *     // Handle another user created event
 * }, 'listener-id-2');
 *
 * Events::instance()->exec('user.created', ["id" => 3, "name" => "ucscode"]);
 * ```
 *
 * ***
 *
 * By utilizing the Event class, developers can enhance the flexibility and maintainability of their PHP applications.
 * Different components can listen to and react to events without tight coupling, resulting in modular and loosely
 * coupled code. This promotes better code organization and easier maintenance of the application.
 *
 * @package events
 * @version 2.1.3
 * @link https://github.com/ucscode/events
 */
class Events
{
    protected $events = array();

    private static $instance;

    public static function instance()
    {
        if(self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Sort Order
     *
     * This method is used to determine the sort order of an item based on the provided sorting criteria.
     * It takes an item as a parameter and an array of sorting criteria. If the item is numeric, it will be sorted
     * based on whether it is negative or positive. If the item is a scalar string, it will be sorted based on whether
     * it starts with a symbol or a letter. If the item is of an unhandled type, an exception will be thrown.
     *
     * @param mixed  $item   The item to determine the sort order
     * @param array  $sort   The array of sorting criteria
     *
     * @return int  The sort order of the item
     *
     * @throws \Exception  When the item is of an unhandled type
     * @ignore
     */
    protected function sort_order($item, array $sort)
    {
        if(is_numeric($item)) {
            /**
             * Sort Numbers
             */
            return ($item < 0) ? $sort['negative'] : $sort['positive'];
        } elseif(is_scalar($item)) {
            /**
             * Sort String
             */
            return (!preg_match("/^[a-z]/i", $item)) ? $sort['symbol'] : call_user_func(function () use ($item, $sort) {
                return ctype_upper($item[0]) ? $sort['uppercase'] : $sort['lowercase'];
            });
            /**
             * Throw Exception!
             */
        } else {
            throw new \Exception("Unhandled type: " . gettype($item));
        }
    }

    /**
     * Compare
     *
     * This method is used to compare two values based on the provided sorting criteria. It uses the
     * "sort_order" method to determine the sort order of each value. If the sort order of the first value
     * is different from the sort order of the second value, the method returns the result of the comparison.
     * If the sort order of both values is the same, the method falls back to a regular comparison using the
     * spaceship operator ("<=>").
     *
     * @param mixed  $a     The first value to compare
     * @param mixed  $b     The second value to compare
     * @param array  $sort  The array of sorting criteria
     *
     * @return int  The result of the comparison
     * @ignore
     */
    protected function compare($a, $b, $sort)
    {
        $res = $this->sort_order($a, $sort) <=> $this->sort_order($b, $sort);
        if($res) {
            return $res;
        }
        return $a <=> $b;
    }

    /**
     * Sort Events
     *
     * This method is used to sort the event list based on a specific order. It takes an array of events as a reference
     * parameter and an optional boolean parameter to specify the order of sorting. If the order is set to `true`,
     * the events will be sorted in ascending order, otherwise, they will be sorted in descending order.
     *
     * @param array  &$eventList  The array of events to be sorted
     * @param bool   $order       Optional. The order of sorting (`true` for ascending, `false` for descending, `null` to ignore sorting)
     *
     * @return void
     * @ignore
     */
    protected function sortEvents(array &$eventList, ?bool $order)
    {

        if(!is_null($order)) {

            /** Sort Events */
            $sort = array(
                'negative' => 0,
                'uppercase' => 1,
                'symbol' => 2,
                'lowercase' => 3,
                'positive' => 4
            );

            /** Reverse the order */
            if(!$order) {
                $sort = array_combine(array_keys($sort), array_reverse(array_values($sort)));
            }

            $convertType = function ($value) {
                if(preg_match("/^\-?\d+/i", $value)) {
                    $value = (float)$value;
                }
                return $value;
            };

            /** Now Sort */
            uksort($eventList, function ($a, $b) use ($sort, $convertType) {
                $a = $convertType($a);
                $b = $convertType($b);
                return $this->compare($a, $b, $sort);
            });

        };

    }

    /**
     * Execute the specified event.
     *
     * This method executes the callbacks associated with the specified event name.
     *
     * @param string $eventName The name of the event to execute.
     * @param array|null $data Optional data to pass to the event callbacks.
     * @param bool|null $sort Flag to indicate whether to sort the event callbacks.
     *        - `null` indicates no sorting.
     *        - `true` indicates sorting in ascending order.
     *        - `false` indicates sorting in descending order.
     *
     * @return void
     */
    public function exec(string $eventName, ?array $data = null, ?bool $sort = true)
    {

        // get the event type to execute;

        $eventName = trim($eventName);

        if(!array_key_exists($eventName, $this->events)) {
            return;
        }

        /**
         * # Order Of Sorting
         *
         * 1. Negative Number
         * 2. Uppercase Letters
         * 3. Symbols
         * 4. Lowercase Letters
         * 5. Positive Numbers
         *
         * # Sorting values
         *
         * - `NULL` == No sorting
         * - `TRUE` == Sort ascending
         * - `FALSE` ==  Sort descending
        */

        $eventList = &$this->events[ $eventName ];

        $this->sortEvents($eventList, $sort);

        # Loop and execute each individual event

        foreach($eventList as $key => $callback) {

            # Call all associated event

            if(is_null($callback)) {
                continue;
            }

            call_user_func_array($callback, [&$data, &$eventName]);

        };

    }


    /**
     * Add Listener
     *
     * This method is used to add a listener for one or multiple events. The listener is associated with
     * a callback function that will be executed when the event is triggered. The event names should be
     * passed as a comma-separated string. Optionally, an event ID can be provided to uniquely identify
     * the listener for a specific event.
     *
     * **Note:** Existing events will not be overwritten.
     * - To check for an existing event and event ID, use the `Events::instance()->hasListener()` method.
     * - To remove an event, use the `Events::instance()->removeEvent()` method.
     *
     * @param string          $eventNames  The name or names of the event(s) to listen to (comma-separated)
     * @param callable|null   $callback    The callback function to be executed when the event is triggered
     * @param string|null     $eid         The event ID to uniquely identify the listener (optional)
     *
     * @return void
     */
    public function addListener(string $eventNames, ?callable $callback, ?string $eid = null)
    {

        # split the events by comma

        $this->splitEvents($eventNames, function ($event) use ($callback, $eid) {

            # If the event name doesn't already exist, create

            if(!array_key_exists($event, $this->events)) {
                $this->events[ $event ] = array();
            };

            # Get reference to the eventlist

            $eventList = &$this->events[ $event ];

            # If no event ID is given, add the event using PHP Array incrementing index

            if(is_null($eid)) {

                $eventList[] = $callback;

            } elseif(!array_key_exists($eid, $eventList)) {

                # Else, if the eventID does not exist, add the event.

                $eventList[ $eid ] = $callback;

            }

        });
    }

    /**
     * Remove Event Listener
     *
     * This method is used to remove event listeners for an event names. It takes the event names
     * as a string and an event ID, and removes the corresponding event listeners from the internal
     * event registry.
     *
     * @param string    $eventName  The event names
     * @param string    $eid         The event ID to identify a specific event listener
     *
     * @return void
     */
    public function removeListener(string $eventName, ?string $eid)
    {

        $status = false;

        # Get the event by reference
        $eventList = &$this->events[ $eventName ] ?? null;

        # If the eventname does not exist, ignore
        if(empty($eventList)) {

            $status = true;

        } else {

            # Remove the specified event
            if(is_null($eid)) {

                unset($this->events[ $eventName ]);
                $status = true;

            } elseif(array_key_exists($eid, $eventList)) {

                unset($eventList[ $eid ]);
                $status = true;

            }

            # Finally: Trash empty event;
            if(empty($eventList)) {
                $this->clear($event);
            };

        };

        return $status;

    }


    /**
     * Clear Events
     *
     * This method is used to clear one or more events from the internal event registry. It takes the event names
     * as a string and removes the corresponding events from the registry.
     *
     * @param string    $eventNames  The comma-separated string of event names
     *
     * @return void
     */
    public function clear(string $eventNames)
    {
        $this->splitEvents($eventNames, function ($event) {
            # If the event exists, delete it
            if(array_key_exists($event, $this->events)) {
                unset($this->events[ $event ]);
            };
        });
        return true;
    }


    /**
     * Get Listener
     *
     * This method is used to retrieve a specific listener for the given event name and event ID.
     * It returns the `callable` associated with the event and event ID, or `null` if no listener is found.
     *
     * @param string    $eventName  The name of the event
     * @param string|null    $eid    The event ID
     *
     * @return array|callable|null The list of associated events. If event ID is given, returns the `callable` associated with the event ID, or `null` if neither the event nor event ID is found
     */
    public function getListener(string $eventName, ?string $eid = null)
    {
        # Get the eventList, or return null
        $eventList = $this->events[ trim($eventName) ] ?? null;

        if(is_null($eid)) {
            return $eventList;
        } elseif(!empty($eventList) && array_key_exists($eid, $eventList)) {
            return $eventList[$eid];
        };

    }


    /**
     * Has Listener
     *
     * This method is used to check if a listener exists for the given event name and optional event ID.
     *
     * @param string    $eventName  The name of the event
     * @param string|null    $eid    The optional event ID
     *
     * @return bool     `true` if a listener exists, `false` otherwise
     */
    public function hasListener(string $eventName, ?string $eid = null)
    {
        return !!$this->getListener($eventName, $eid);
    }

    /**
     * List Listeners
     *
     * This method is used to retrieve a list of registered listeners. It can return a list of all event names or a specific list of listeners for a given event name.
     *
     * @param bool      $verbose       Whether to include priority information in the list (default: `false`)
     * @param string|null    $eventName  The optional event name to filter the list
     *
     * @return array    An array containing a list of listeners. If `$verbose` is `false`, an array of event names. If `$verbose` is true, an associative array with event names as keys and their corresponding listeners as values. If `$eventName` is provided, an array of listeners for the specific event name.
     */
    public function list($verbose = false, ?string $eventName = null)
    {
        if(!$verbose) {
            return array_keys($this->events);
        };
        return is_null($eventName) ? $this->events : ($this->events[$eventName] ?? null);
    }

    /**
     *
     */
    private function __construct()
    {

    }

    /**
     * Split Events
     *
     * This method is used internally to split a comma-separated string of event names into individual
     * event names. It takes the event names as a string and a callback function, and invokes the callback
     * function for each individual event name.
     *
     * @param string    $eventNames  The comma-separated string of event names
     * @param callable  $func        The callback function to be invoked for each event name
     *
     * @return void
     * @ignore
     */
    private function splitEvents(string $eventNames, callable $func)
    {
        $eventNames = array_map("trim", explode(",", $eventNames));
        foreach($eventNames as $eventName) {
            $func($eventName);
        }
    }

};

Events::instance();
