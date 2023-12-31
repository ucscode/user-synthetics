<?php

namespace Ucscode\UssForm;

use Ucscode\UssElement\UssElement;
use Ucscode\UssForm\Field\Field;
use Ucscode\UssForm\Form\Form;

require_once 'autoload.php';

$form = new Form();

$collection = $form->getCollection('default');
$collection->addField('main', new Field());

$field = new Field(Field::NODE_INPUT, Field::TYPE_SWITCH);
$collection->addField("user[name]", $field);

$context = $collection->getElementContext();
$context->container
    ->setAttribute('name', 'true')
    ->setAttribute('block', 'flow')
    ->setAttribute('name', 'electoral', true)
    ->removeAttribute('name', 'true')
    ->setValue('name')
;

$context->instruction
    ->setValue("Fill this form or else")
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
$fieldContext->widget->setHidden(true);
$fieldContext->widget->setHidden(false);
$fieldContext->prefix
    ->setValue("click")
;
$fieldContext->suffix
    ->setValue(new UssElement(UssElement::NODE_BUTTON))
    ->setValue("cole")
    ->setValue(new UssElement(UssElement::NODE_BUTTON))
    ->setValue(null);

$fieldContext->widget
    ->setValue("color")
    ->setDOMHidden(1)
    ->setAttribute("data-cold", "I'm not \"here\"")
    ->addClass("model faster")
;

$fieldContext->info
    ->setValue("Please select one of the many options")
;

$collection->addField("main2", (new Field(Field::NODE_TEXTAREA)));

//$collection->removeField("user[name]");
//$collection->setFieldPosition($collection->getField('main2'), Collection::POSITION_BEFORE, $field);

var_dump($context->export());
