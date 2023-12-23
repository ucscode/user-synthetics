<?php

namespace Uss\Component\Event;

final class Event
{
    private static $eventList = [];

    public static function emit(string $name, array $data = [])
    {
        self::execAll(self::$eventList[$name] ?? [], $data);
    }

    public function addListener(string $name, EventInterface|callable $action, float $order = 0): self
    {
        $eventList = &self::$eventList;
        if(!array_key_exists($name, $eventList)) {
            $eventList[$name] = [];
        };
        $eventList[$name][] = [
            'action' => $action,
            'order' => $order
        ];
        return $this;
    }

    private static function execAll(array $list, array|object $data)
    {
        usort($list, function ($a, $b) {
            return $a['order'] <=> $b['order'];
        });
        foreach($list as $event) {
            $action = $event['action'];
            if($action instanceof EventInterface) {
                $action->eventAction($data);
            } else {
                call_user_func($action, $data);
            }
        };
    }

}
