# USS Form Documentation

Build Fully Customized HTML Form in 1 Minute

## Information

The UssForm is a powerful independent Form builder library that enable you create and configure complex HTML Form in minutes. <br/>
The component was originally created for [User Synthetics](https://github.com/ucscode/user-synthetics) Project but can easily be integrated into any other platform.
It utilizes the class names of bootstrap 5 for styling but allows you to easily modify it if you do not use bootstrap 5 in your project. <br>

### Instantiation:

Instantiate the UssForm Class. The class constructor contains the following parameters

- `$name`: The name of the form
- `$route` (optional): The url location ( `action` ) where the form will be submitted to. Defaults to the current webpage
- `$method`: The request method ( e.g `POST`, `GET` )
- `$enctype`: The encryption type ( e.g `multipart/form-data` );

```php
$form = new UssForm('contact', '/contact', 'POST', 'multipart/form-data');
```

### Adding Fields

The `UssForm::add()` method allows you to append new field to your form. It accepts 4 arguments

- `$name`: The name of the field ( e.g the key that will be available in the `$_POST` variable )
- `$fieldType`: This is the type of field to be created, which can be one of the following:
    1. `UssForm::INPUT`
    3. `UssForm::SELECT`
    2. `UssForm::TEXTAREA`
    4. `UssForm::BUTTON`
- `$context`: The context associated with the field types, explained as follows:
    1. `UssForm::INPUT`: In this case, context is the input type such as `text`, `number`, `date` etc
    2. `UssForm::SELECT`: In this case, context is an `array` of options for the input. The array key is the input value while the array value is what will display to the user
    3. `UssForm::TEXTAREA`: In this case, context is not used an can be set to `null`
    4. `UssForm::BUTTON`: In this case, context represents the button tag name which can be either `input` or `button`
- `$config`: This is an array with options to modify the input field element such as setting input-group icons, adding custom classes etc. Advance configuration can be achieved by passing a callback to the `fields` index. The callback receives an array an argument which contains all the element within the field. By accessing each individual element, you can make unrestricted and complete customization of the field

> The return value of `UssForm::add()` is the element that was added to the form

### Examples

#### Adding a simple field

```php
$form->add("name", UssForm::INPUT);
```

#### Adding a fully configured field

```php
# Configured Field
$form->add("email", UssForm::INPUT, UssForm::TYPE_TEXT, [
    "label" => "Enter your email address", // use a custom label
    "value" => "uche23mail@gmail.com", // add a default value
    "group" => [
        "prepend" => '$', // prepend icon or button
        "append" => (new UssElementBuilder('button'))->setContent('send') // append icon or button
    ],
    "report" => [
        'message' => "Sorry, the email address is wrong", // display message for the field
        'class' => 'text-danger' // custom report class
    ],
    "id" => "custom_id", // set custom id,
    "class" => "custom_class", // set custom class,
    "column" => "col-lg-9 mb-3", // set custom column size
    "fields" => function($fields) {
        // custom configuration;
        $fields['widget']
            ->setAttribute('data-name', 'crop')
            ->addAttributeValue('class', 'custom-class-by-callable');
    }
]);
```

#### Managing Rows

Each added field will be append into a row block.

```html
<div class='row'>
    ...Field Goes Here
    ...Another Field Goes Here
    ...The next goes here
</div>
```

To add new row, you can utilize the `UssForm::addRow()` method. <br>
The method accept 1 optional parameter which is the class name that will be appended to the row. <br>
Once the row is created internally, the next set of fields will begin to append into the newly created row

```php
$form->add(); 
/* 
<div class='row'>
    Fields are added to this row
</div> 
*/

$form->add('mb-3'); 
/*
<div class='row mb-3'>
    Now fields begin to add to this row
</div>
*/
```
#### Adding Select Fields

The third parameter of a select field accepts an array which represents the options of the field. Example:

```php
$options = array( "The actual value" => "What user will see in the dropdown" );
```

- The key of the array represents the real value of each option
- The value of the array represent what will be displayed to user.
```php
$countries = [
    "USD" => "US Dollar",
    "EUR" => "Euro",
    "GBP" => "Pounds Sterling"
];

$form->add("country", UssForm::SELECT, $countries, [
    'value' => 'EUR', // select as default value
    'label' => "Please select your country"
]);
```

#### Adding Textarea Field

Textarea field works similar to input field

```php
$form->add("product[description]", UssForm::TEXTAREA, null, [
    'value' => "This is a context you cannot believe",
    'label' => "What are your thoughts?",
    "column" => "col-lg-7 mb-3",
]);
```

#### Adding CheckBoxes

UssForm also simplifies the process of adding checkboxes. <br>
Similiar to the input field, the checkbox field category has a range of settings to customize or change the behaviour of the field

```php
// Adding a basic checkbox
$form->add('mycheckbox', UssForm::INPUT, UssForm::TYPE_CHECKBOX);

// Adding a radio button
$form->add('myradio', UssForm::INPUT, UssForm::TYPE_RADIO);

// Adding a switch with an initial checked state
$form->add("myswitch", UssForm::INPUT, UssForm::TYPE_SWITCH, [
    'checked' => true
]);
```

#### Adding Hidden Field

Hidden fields do not display on HTML pages but are added. <br>
They are often used to store information that does not need to be changed by users.<br>
Thus, they don't have much options to configure such as label and column. <br>
However, they can still be configured and customized based on specific requirement by using a callback in the `field` index

```php
$form->add("secretkey", UssForm::INPUT, UssForm::TYPE_HIDDEN, [
    'value' => "dd947da3-1e85-4e17-a0ff-520c1664763c"
]);
```
#### Adding Submit Button

Before rending your form in HTML, you need to add a submit button. <br>
This is not done automatically, so you need to specify where you want the submit button to appear

```php
$form->add('submit', UssForm::BUTTON, null, [
    'class' => 'btn btn-danger',
    'use_name' => true,
    'content' => "Custom Display Text"
]);
```

If you want to use `<input type='submit'>` instead of `<button type='submit'></button>`, you should pass `UssForm::INPUT` as the third parameter

### Rendering Your Form

When you've fully built and configured your form, you can get the HTML string by calling `UssForm::getHTML()` method <br>
You can also set the method's parameter to true to indent the HTML output:

```php
echo $form->getHTML(true);
```

### HTML Sample

The combination of all code explained above will result to the output below:

```html
<form name="contact" action="/contact" method="POST" enctype="multipart/form-data" id="_ussf_contact">
	<div class="row">
		<div class="col-md-12 mb-3" id="_ussf_contact_name_column">
			<label class="form-label">Name:</label>
			<div class="input-single">
				<input name="name" type="text" class="form-control" id="_ussf_contact_name_widget"/>
			</div>
			<div class="form-text form-report" id="_ussf_contact_name_report"></div>
		</div>
		<!-- other field goes here -->
	</div>
	<div class="row mb-5">
		<div class="col-md-12 mb-3" id="_ussf_contact_country_column">
			<label class="form-label">Please select your country</label>
			<div class="input-single">
				<select name="country" class="form-select" id="_ussf_contact_country_widget">
					<option value="USD">US Dollar</option>
					<option value="EUR" selected="selected">Euro</option>
					<option value="GBP">Pounds Sterling</option>
				</select>
			</div>
			<div class="form-text form-report" id="_ussf_contact_country_report"></div>
		</div>
		<!-- other field goes here -->
		<input name="secretkey" type="hidden" class="form-control" value="dd947da3-1e85-4e17-a0ff-520c1664763c"/>
		<div class="col-md-12 mb-3" id="_ussf_contact_submit_column">
			<button type="submit" class="btn btn-danger" name="submit" id="_ussf_contact_submit_widget">Custom Display Text</button>
		</div>
	</div>
</form>
```

## Populating Form Fields

You have the ability to populate the form fields with data before rendering the form. <br>
This can be accomplished using the `UssForm::populate()` method, which takes an array containing values for the form fields.

```php
$form->populate([
    'email' => 'custom@email.com',
    'country' => 'GBP',
    'product[description]' => "This is a description, and more...",
    'name' => 'UCSCode'
]);
```

Keep in mind that any values set in the fourth parameter of the `UssForm::add()` method will **override** any populated values. <br>
This allows you to customize specific fields even if you're populating the form with initial data.

## Setting and Getting Widget Value

Different form elements have different ways of setting their values. For example;

- The value of `input` element is set by its `value` attribute.
- The value of a `select` element is set by adding the `selected` attribute to one of its child `option` elements
- The value of a `textarea` element is set by adding the text within its opening and closing tags. 

To simplify the process and avoid excessive parsing of nodes, **UssForm** provides convenient methods for setting and getting values of form field widgets using `UssForm::setValue()` and `UssForm::getValue()`.

### Setting Widget Value

To set the value of a form field widget, you can use the `UssForm::setValue()` method. This method accepts two arguments:

1. The first argument is the field widget (element) you want to set the value for.
2. The second argument is a string representing the value you want to set.

```php
// Let's create a new Textarea Widget
$textarea = UssElementBuilder('textarea'); 

$form->setValue($textarea, 'This is a value'); // true
```

### Getting Widget Value

To retrieve the value of a form field widget, you can use the `UssForm::getValue()` method. <br>
This method accepts one argument, which is the field widget (element) from which you want to retrieve the value.

```php
$value = $form->getValue($textarea); // 'This is a value'
```

These methods make it convenient to interact with and manipulate the values of form field widgets within the **UssForm** library.

### Acknowledgment

The UssForm library is a part of the <a href='https://github.com/ucscode/user-synthetics'>User Synthetics</a> project developed by UCSCode.

### Contact Information

For inquiries and support related to the UssForm library, visit <a href='https://ucscode.me'>User Synthetics Website</a>

### Dependency on UssElementBuilder

Please note that the UssForm library relies on the UssElementBuilder library developed by UCSCode for some of its functionality. Make sure to include the UssElementBuilder library in your project as it is a required dependency for UssForm to work correctly. You can find the UssElementBuilder library at https://github.com/ucscode/uss-element-builder.