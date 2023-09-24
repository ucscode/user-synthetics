<?php

use Ucscode\Event\EventInterface;
use Ucscode\Event\Event;

require __DIR__ . "/EventInterface.php";
require __DIR__ . "/Event.php";

$priority = 20;

(new Event())

    ->addListener('UserDeleted', function (array $data) {

        $data['action'] = 'Function Expression: @' . __FUNCTION__;
        var_dump($data);

    }, $priority)

    ->addListener('UserDeleted', new class () implements EventInterface {
        public function eventAction(array $data): void
        {
            $data['action'] = 'Class Expression: @' . __CLASS__;
            var_dump($data);
        }

    });


(new Event())->dispatch('UserDeleted', [
    'name' => 'Ucscode'
]);
