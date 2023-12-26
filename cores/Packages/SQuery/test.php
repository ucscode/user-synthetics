<?php

namespace Ucscode\SQuery;

use mysqli;

spl_autoload_register(function ($classname) {
    $basename = basename(str_replace("\\", "/", $classname));
    require_once __DIR__ . "/{$basename}.php";
});

$mysqli = new mysqli('localhost', 'root', '12345678', 'www_uss');

$condition = (new Condition())
    ->add("film", "musa's")
    ->or("game", ["post", "class", "video"])
    ->and("user.name", ['voldka', "spider.crown"], null)
    ->customFilter("AND (username LIKE orange)")
    ->or('slash', ['model'], 'LIKE')
    ->and('slope', '5, 6', 'BETWEEN', null)
    ->and('SUM(cols)', ['g', 4, 2.2, 'Game'], '>');

$squery = (new SQuery())
    ->select()
    ->from("frame", 'p')
    ->where($condition)
    ->orderBy([
        "user.name" => 'ASC',
        "portal" => "DESC",
        "gold",
        "frog",
        "model" => "DESC"
    ])
    ->orderBy("frame")
    ->offset(20)
    ->limit(5);

var_dump($squery->build());

// $query = (new SQuery())->select()
//     ->from('tablename', 't')
//     ->leftJoin('goat', 'g')
//     ->on('t.name', 'g.game')
//     ->rightjoin('water', 'm')
//     ->crossjoin('muted')
//     ->or([
//         'name' => null,
//         'client' => SQuery::IS_NULL,
//         'color' => ['blue', 'black', 'green']
//     ], null, 'REGEXP')
//     ->having('name', '12')
//     ->having([
//         'group' => 'flame',
//         'span'
//     ])
//     ->orderBy('name')
//     ->orderBy('class', 'voice')
//     ->orderBy([
//         'animal' => 'fish',
//         'goat' => 'hen'
//     ], null, 'RLIKE')
//     ->groupBy(['f.user', 'port.code']);

// $update = (new SQuery())->update("tablename", [
//     'username' => 'ucscode',
//     'password' => 'trust'
// ]);

// $update->where('client', 'id');

// $update->set('self.cold', 'whort');

// var_dump($update->getQuery());
