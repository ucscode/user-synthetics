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
 * Events::addListener("user.created", function($data) {
 *     // Handle user created event
 * }, 'listener-id-1');
 *
 * Events::addListener("user.created", function($data) {
 *     // Handle another user created event
 * }, 'listener-id-2');
 *
 * Events::exec('user.created', ["id" => 3, "name" => "ucscode"]);
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
    protected static $events = array();

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
    protected static function sort_order($item, array $sort)
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
    protected static function compare($a, $b, $sort)
    {
        $res = self::sort_order($a, $sort) <=> self::sort_order($b, $sort);
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
    protected static function sortEvents(array &$eventList, ?bool $order)
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
                return self::compare($a, $b, $sort);
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
     * @param Closure|null $master Optional: master closure to enforce conditions on other events.
     *        The master closure can determine which event is running and set conditions for it to run.
     *        The closure should return `false` (not `null`) to cancel an event.
     * @return void
     */
    public static function exec(string $eventName, ?array $data = null, ?bool $sort = true)
    {

        // get the event type to execute;

        $eventName = trim($eventName);

        if(!array_key_exists($eventName, self::$events)) {
            return;
        }

        /**
         * SORTING ORDER
         * ---------------------
         *
         * 1. Negative Number
         * 2. Uppercase Letters
         * 3. Symbols
         * 4. Lowercase Letters
         * 5. Positive Numbers
         *
         * ------------------------
         *
         * - `NULL` == No sorting
         * - `TRUE` == Sort ascending
         * - `FALSE` ==  Sort descending
        */

        $eventList = self::$events[ $eventName ];

        self::sortEvents($eventList, $sort);

        /**
         * Loop and execute each individual event
         */

        foreach($eventList as $key => $action) {

            /**
             * Skip empty callable
             *
             * A callable may be passed as null if you want to override an event and cancle its effect
             */
            if(is_null($action['callable'])) {
                continue;
            }


            // define the event offset

            $action['debugger']['offset'] = $key;
            
            # Call User Function;
            
            call_user_func($action['callable'], $data ?? [], $eventName);

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
     * - To check for an existing event and event ID, use the `Events::hasListener()` method.
     * - To remove an event, use the `Events::removeEvent()` method.
     *
     * @param string          $eventNames  The name or names of the event(s) to listen to (comma-separated)
     * @param callable|null   $callback    The callback function to be executed when the event is triggered
     * @param string|null     $eid         The event ID to uniquely identify the listener (optional)
     *
     * @return void
     */
    public static function addListener(string $eventNames, ?callable $callback, ?string $eid = null)
    {
        /**
         * Get 3 level debugger
         * Thi will be passed to the master executor
         */
        $debug = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 3);

        /** eid = Event ID */

        /**
         * Split Multiple Events
         */
        self::splitEvents($eventNames, function ($event) use ($callback, $eid, $debug) {
            /**
             * If the event name doesn't already exist, create
             */
            if(!array_key_exists($event, self::$events)) {
                self::$events[ $event ] = array();
            }
            /**
             * Get the eventlist
             */
            $eventList = &self::$events[ $event ];
            /**
             * Create the event data
             */
            $action = array(
                "callable" => $callback,
                "debugger" => $debug,
            );
            /**
             * If no eventID is given,
             * Add the event using PHP Array incrementing index as the eventID
             */
            if(is_null($eid)) {
                $eventList[] = $action;
            }
            /**
             * Else, if the eventID does not exist, add the event.
             */
            elseif(!array_key_exists($eid, $eventList)) {
                $eventList[ $eid ] = $action;
            }

        });
    }

    /**
     * Remove Event Listener
     *
     * This method is used to remove event listeners for one or more event names. It takes the event names
     * as a string and an optional event ID, and removes the corresponding event listeners from the internal
     * event registry.
     *
     * @param string    $eventNames  The comma-separated string of event names
     * @param string    $eid         (Optional) The event ID to identify a specific event listener
     *
     * @return void
     */
    public static function removeListener(string $eventNames, ?string $eid = null)
    {
        /**
         * Split Multiple Events
         */
        self::splitEvents($eventNames, function ($event) use ($eid) {
            /**
             * Handle Individual Event
             * If the eventname does not exist, ignore
             */
            if(!array_key_exists($event, self::$events)) {
                return;
            }

            /**
             * Get the event by reference
             */
            $eventList = &self::$events[ $event ];

            /**
             * If no eventID is given,
             * Remove the last add event
             */
            if(is_null($eid) && !empty($eventList)) {
                array_pop($eventList);
            }

            /**
             * Else, remove the specified event
             */
            elseif(array_key_exists($eid, $eventList)) {
                unset($eventList[ $eid ]);
            }

            /**
             * Finally: Trash empty event;
             */
            if(empty($eventList)) {
                self::clear($event);
            }
        });
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
    public static function clear(string $eventNames)
    {
        /**
         * Split Multiple Events
         */
        self::splitEvents($eventNames, function ($event) {
            /**
             * If the event exists, delete it
             */
            if(array_key_exists($event, self::$events)) {
                unset(self::$events[ $event ]);
            }
        });
    }


    /**
     * Get Listener
     *
     * This method is used to retrieve a specific listener for the given event name and optional event ID.
     * It returns the `callable` associated with the event and event ID, or `null` if no listener is found.
     *
     * @param string    $eventName  The name of the event
     * @param string|null    $eid    The optional event ID
     *
     * @return callable|null The `callable` associated with the event and event ID, or `null` if not found
     */
    public static function getListener(string $eventName, ?string $eid = null)
    {
        /**
         * Get the eventList
         * Or return null
         */
        $eventList = self::$events[ trim($eventName) ] ?? null;
        if(empty($eventList)) {
            return;
        }

        /**
         * If eventID is null, get the last added callable
         */
        if(is_null($eid)) {
            /**
             * Get the last event
             */
            $event = end($eventList);

            if($event) {
                return $event['callable'];
            }

        } elseif(array_key_exists($eid, $eventList)) {
            return $eventList[$eid]['callable'];
        }

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
    public static function hasListener(string $eventName, ?string $eid = null)
    {
        return !!self::getListener($eventName, $eid);
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
    private static function splitEvents(string $eventNames, callable $func)
    {
        $eventNames = array_map("trim", explode(",", $eventNames));
        foreach($eventNames as $eventName) {
            $func($eventName);
        }
    }


    /**
     * List Listeners
     *
     * This method is used to retrieve a list of registered listeners. It can return a list of all event names or a specific list of listeners for a given event name.
     *
     * @param bool      $priority       Whether to include priority information in the list (default: `false`)
     * @param string|null    $eventName  The optional event name to filter the list
     *
     * @return array    An array containing a list of listeners. If `$priority` is `false`, an array of event names. If `$priority` is true, an associative array with event names as keys and their corresponding listeners as values. If `$eventName` is provided, an array of listeners for the specific event name.
     */
    public static function list($priority = false, ?string $eventName = null)
    {
        if(!$priority) {
            return array_keys(self::$events);
        }
        $list = array();
        array_walk(self::$events, function ($data, $key) use (&$list) {
            $list[ $key ] = $list[ $key ] ?? [];
            foreach($data as $id => $value) {
                unset($value['debugger']);
                $list[ $key ][ $id ] = $value['callable'];
            };
        });
        return is_null($eventName) ? $list : ($list[$eventName] ?? null);
    }

};
