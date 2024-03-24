<?php

namespace Uss\Component\Event;

use Uss\Component\Trait\SingletonTrait;

class Event
{
    use SingletonTrait;

    protected array $listeners = [];
    
    public function addListener(string $name, EventInterface|callable $action, float $order = 0): self
    {
        $this->listeners[$name] ??= [];
        $this->listeners[$name][] = [
            'action' => $action,
            'order' => $order
        ];
        return $this;
    }

    public function removeListener(string $name, EventInterface|callable $action): self
    {
        if (isset($this->listeners[$name])) {
            foreach ($this->listeners[$name] as $key => $listener) {
                if ($listener['action'] === $action) {
                    unset($this->listeners[$name][$key]);
                }
            }
        }
        return $this;
    }
    
    public function dispatch(string $name, mixed $data = null): self
    {
        if(array_key_exists($name, $this->listeners)) {
            usort($this->listeners[$name], fn ($a, $b) => $a['order'] <=> $b['order']);
            foreach($this->listeners[$name] as $event) {
                $action = $event['action'];
                $action instanceof EventInterface ? $action->eventAction($data) : call_user_func($action, $data);
            };
        }
        return $this;
    }

}
