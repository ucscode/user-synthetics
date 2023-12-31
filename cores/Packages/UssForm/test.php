<?php

namespace Ucscode\UssForm;

use Ucscode\UssElement\UssElement;
use Ucscode\UssForm\Collection\Collection;
use Ucscode\UssForm\Field\Field;
use Ucscode\UssForm\Form\Form;
use Ucscode\UssForm\Gadget\Gadget;
use Ucscode\UssForm\Resource\Facade\Position;
use Ucscode\UssForm\Widget\Widget;

require_once 'autoload.php';

$form = new Form();
$form->getAttribute()->setAction('block');

$collection = $form->getCollection('default');
$collection->addField('main', new Field());

$field = new Field(Field::NODE_INPUT, Field::TYPE_NUMBER);
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

$checkbox = new Gadget(UssElement::NODE_INPUT, Field::TYPE_CHECKBOX);
$checkbox->label->setValue("Checkbox");
$field->addGadget("checkbox", $checkbox);

$field->addGadget("button", (new Gadget(Field::NODE_INPUT, Field::TYPE_BUTTON)));
$button = $field->getGadget("button")->widget->setButtonContent("Button", true);

$field->addGadget("select", new Gadget(Field::NODE_SELECT));
$select = $field->getGadget("select")->widget->setOptions([
    "location" => "USA",
    "Country" => "Africa",
    "Position" => "Innert"
]);

$default = $field->getElementContext()->gadget;

//$collection->removeField("user[name]");
//$collection->setFieldPosition($collection->getField('main2'), Collection::POSITION_BEFORE, $field);

$form->addCollection("driver", new Collection());
$voltex = $form->getCollection("driver")->getElementContext();
$voltex->title->setValue("we are here");
$voltex->subtitle->setDOMHidden(true);
$voltex->instruction->setDOMHidden(true);
;

$field->setGadgetPosition("select", Position::BEFORE, 'checkbox');
$field->setGadgetPosition($fieldContext->gadget, Position::BEFORE, 'button');

var_dump($form->export());

/*

{{ form.export() }}

*/