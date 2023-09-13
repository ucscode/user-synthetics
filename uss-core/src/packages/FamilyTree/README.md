# FamilyTree

FamilyTree is a versatile hierarchical tree generator for webpages that allows you to easily create and manage tree structures. It provides a convenient way to build parent-child relationships and create a hierarchy of menus or any other hierarchical data.

## How FamilyTree Works

The `FamilyTree` class provides several methods to create and manipulate menus or hierarchy:

### Adding Nodes

To create a new node with a name and attributes, you can use the `add` method of the `FamilyTree` class:

```php
$familyTree = new FamilyTree();

// Creating a webpage menu hierarchy with familyTree

$main_menu = $familyTree->add("main-menu", [
	"label" => "dashboard",
	"key" => "value"
]);

$child_menu = $mainMenu->add("child-menu");
```

The `add` method returns a new instance of the added `FamilyTree` object, allowing you to create a parent-child relationship and build a hierarchy of menus.

### Accessing Child Menus

To access a specific child menu using a reference variable, you can use the `get` method. For example:

```php
$child_menu = $familyTree->get("main-menu")->get("child-menu");
```

This code starts from the root menu, finds the menu with the name "main-menu," and retrieves the child menu with the name "child-menu."\
You can traverse multiple levels of menu trees using this method.

### Obtaining Parent Menu

You can obtain the parent menu of a child menu by accessing the `parentMenu` property of a `FamilyTree` instance:

```php
$child_menu->parentMenu; // FamilyTree Instance
```

This code retrieves the `FamilyTree` instance of the parent menu.

### Removing Menus

The `remove` method allows you to remove a child `FamilyTree` object from its parent. You can remove a menu by providing its name:

```php
$main_menu->remove("child-menu"); // remove "child-menu" and all its children

$familyTree->remove("main-menu"); // remove "main-menu" and all its children
```

The `remove` method provides a way to dynamically remove specific nodes from the hierarchy when needed.

### Managing Attributes

FamilyTree allows you to manage attributes associated with menus:

- To retrieve the value of a specific attribute, you can use the `getAttr` method:

  ```php
  $main_menu->getAttr('label'); // returns "dashboard"
  ```

- To set the value of an attribute, you can use the `setAttr` method:

  ```php
  $main_menu->setAttr('label', "profile"); // overwrite previous label
  $main_menu->setAttr('title', "Menu Title"); // create a new attribute named "title"
  ```

- To remove an attribute from a menu, you can use the `removeAttr` method:

  ```php
  $main_menu->removeAttr('label'); // label attribute removed
  ```

## Menu Structure Demonstration:

Here's an example of how FamilyTree can be used to create a dynamic menu with a parent-child hierarchy:

```php
$familyTree = new FamilyTree();
	
$main_menu = $familyTree->add("menu-name", [
    "label" => "Home",
    "href" => "//example.com"
]);

$child_menu1 = $main_menu->add("child-menu-1", [
    "label" => "About",
    "href" => "//example.com/about"
]);

$child_menu2 = $main_menu->add("child-menu-2", [
    "label" => "Services",
    "href" => "//example.com/services"
]);

$sub_child_menu = $child_menu2->add("sub-child-menu", [
    "label" => "Sub Service",
    "href" => "//example.com/services/sub-service"
]);
```

The above example demonstrates how you can create a hierarchical menu structure using FamilyTree.

## Rendering the Menu Structure

To render the menu structure, you can define a **self-recursive** function that traverses the `FamilyTree` object and generates the desired output.\
Here's an example of a self-recursive function called `iterator`:

```php
function iterator($menu) {
    
    if ($familyTree->level > 5) {
        # You can enforce a limit
        throw new Exception("You should not append menu beyond the depth of 5 levels");
    }
    
    $html = "<li>";
    $html .= "<a href='{$menu->getAttr('href')}'>{$menu->getAttr('label')}</a>";

    if (!empty($menu->child)) {
        $html .= "<ul class='child'>";
        foreach ($menu->child as $childMenu) {
            $html .= iterator($childMenu);
        }
        $html .= "</ul>";
    }

    $html .= "</li>";
    return $html;
    
};

$html = "<ul class='main-nav'>" . iterator($familyTree) . "</ul>";

echo $html;
```

In this example: 

- The `iterator` function takes a `FamilyTree` object as its parameter. 
- It checks the level property of the `FamilyTree` object to avoid exceeding a certain depth limit. 
- It then generates the HTML markup for the current menu item and recursively calls itself (`iterator`) to generate the HTML for the nested menus.

The resulting HTML structure can be customized to fit your project's design and requirements.

## Versatility of FamilyTree

FamilyTree's versatility extends beyond creating menus. It can be used for managing any kind of parent-child hierarchy. Some examples include:

- Organizational charts
- Directory structures
- User hierarchies

With its robust functionality and flexible nature, FamilyTree can handle and facilitate various scenarios involving hierarchical data. Its intuitive methods and straightforward implementation make it a valuable tool for projects that require efficient management of hierarchical structures.

## Author

Uchenna Ajah @ Ucscode &lt;uche23mail@gmail.com&gt;

