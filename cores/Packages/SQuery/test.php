<?php

namespace Ucscode\SQuery;

spl_autoload_register(function ($classname) {
    $basename = basename(str_replace("\\", "/", $classname));
    require_once __DIR__ . "/{$basename}.php";
});

$condition = (new Condition())
    ->add("film", "nude")
    ->or("game", ["post", "class", "video"])
    ->and("user.name", ['voldka', "spider.crown"], null)
    ->customFilter("AND (username LIKE orange)")
    ->or('slash', ['model'], 'LIKE')
    ->and('slope', null)
    ->and('SUM(cols)', 'false', '>', null);

$squery = (new SQuery())
    ->select(['`table`.name as `pork`', 'name AS g', 'smith', '`code`', 'model.work As GOLD'])
    ->from('users', 'p')
    ->where($condition)
    ->groupBy("u.name")
    ->groupBy(['p.face', 'polar'])
    ->having(
        (new Condition())
            ->and("face", "clous", 'REGEXP')
            ->or("mate", "gaze", "sport")
    )
    ->orderBy("name", "ASC")
    ->orderBy("class", "DESC")
    ->offset(5)
    ->limit(4);

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
