<?php

use Ucscode\SQuery\SQuery;

require_once __DIR__ . "/SQueryInterface.php";
require_once __DIR__ . "/SQueryTrait.php";
require_once __DIR__ . "/AbstractSQuery.php";
require_once __DIR__ . "/SQuery.php";

$query = (new SQuery())->select()
    ->from('tablename', 't')
    ->leftJoin('goat', 'g')
    ->on('t.name', 'g.game')
    ->rightjoin('water', 'm')
    ->crossjoin('muted')
    ->or([
        'name' => null,
        'client' => SQuery::IS_NULL,
        'color' => ['blue', 'black', 'green']
    ], null, 'REGEXP')
    ->having('name', '12')
    ->having([
        'group' => 'flame',
        'span'
    ])
    ->orderBy('name')
    ->orderBy('class', 'voice')
    ->orderBy([
        'animal' => 'fish',
        'goat' => 'hen'
    ], null, 'RLIKE')
    ->groupBy(['f.user', 'port.code']);

$update = (new SQuery())->update("tablename", [
    'username' => 'ucscode',
    'password' => 'trust'
]);

$update->where('client', 'id');

$update->set('self.cold', 'whort');

var_dump($update->getQuery());
