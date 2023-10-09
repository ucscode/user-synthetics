<?php

namespace Ucscode\Event;

use SingletonTrait;
use Ucscode\Event\EventInterface;

final class Event
{
    use SingletonTrait;

    private static $eventList = [];

    public function emit(string $name, array $data = [])
    {
        $this->execAll(self::$eventList[$name] ?? [], $data);
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

    private function execAll(array $list, array $data)
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
