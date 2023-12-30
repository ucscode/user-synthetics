<?php

namespace Ucscode\UssForm;

use Ucscode\UssForm\Field\Field;
use Ucscode\UssForm\Form\Form;

require_once 'autoload.php';

$form = new Form();

$collection = $form->getCollection('default');
$collection->addField('main', new Field());

$field = new Field(Field::NODE_INPUT, Field::TYPE_HIDDEN);
$collection->addField("user[name]", $field);

$context = $collection->getElementContext();
$context->container
    ->setAttribute('name', 'true')
    ->setAttribute('block', 'flow')
    ->setAttribute('name', 'electoral', true)
    ->removeAttribute('name', 'true')
    ->setValue('name')
    ->getAttribute('name')
;

$widget = $field->getElementContext()->widget;

$widget->setOptions([
    "name" => "uche",
    "school" => "DCC",
    "color" => ["black", "best" => "white", "blue"],
    "voice" => new \stdClass(),
    "final" => false,
    "smooth" => "This's the best part in the world\"s camp"
]);

$widget->removeOption('voice');
$widget->setOption("name", "coding");
$widget->setOption("name", "MODEL::SPONGE");

$fieldContext = $field->getElementContext();
$fieldContext->widget->setHidden(false);
var_dump($fieldContext->frame->getElement()->getHTML(1));
