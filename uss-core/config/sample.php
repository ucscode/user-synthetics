<?php
/**
 * This page does nothing.
 * It's only used to check out some feature before the modules load
 * And then the features are cleared
 */

$body = new UssElementBuilder('body');
$div = [];
for($x = 0; $x < 3; $x++) {
    $div[$x] = new UssElementBuilder('div');
    $body->appendChild($div[$x]);
}

$h1 = new UssElementBuilder('H1');
$hr = new UssElementBuilder('hr');

$span = new UssElementBuilder('span');
$span->addProperty('id', 5);
$div[0]->appendChild($span);

$div[0]->addProperty('class', 'span view spo-tt 54 it\'s "andy and grandy"');
$div[0]->addProperty('id', 'model');
$div[0]->setAttribute('data-name', 'money maker');
$div[0]->setAttribute('vue-js');

$h1->appendChild($span);
$h1->appendChild($hr);

$span->appendChild(new UssElementBuilder('view'));

$body->appendChild($h1);
$body->setAttribute("name", "lorem ipsum is dummy");
$body->setAttribute(" data-left", "try again");

$body->getHTML();
