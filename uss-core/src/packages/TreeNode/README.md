# TreeNode

TreeNode is a versatile hierarchical tree generator for webpages that allows you to easily create and manage tree structures. It provides a convenient way to build parent-child relationships and create a hierarchy of nodes or any other hierarchical data.

## How TreeNode Works

The `TreeNode` class provides several methods to create and manipulate nodes or hierarchy:

### Instantiation

Import the `TreeNode` package into your project and instantiate it

```php
use Ucscode\TreeNode\TreeNode;

$treeNode = new TreeNode();
```

### Adding Nodes

To add a child node, use the `add` method of the `TreeNode` class. There are several options

1. Automatic creation of child Node (without attribute): This will create a new instance of `TreeNode` (without attribute) and append it as child of the current Node

```php
$treeNode->add('main-node');
```

2. Automatic creation of child Node with attributes: This will create a new instance of `TreeNode` with attributes and append it as child of the current Node.

```php
$treeNode->add("main-node", [
	"label" => "dashboard",
	"key" => "value"
]);
```
3. Manual creation of child Node: This involves instantiating a new instance of `TreeNode` and adding it as a child of the current Node

```php
$childNode = new TreeNode('child-name', [
    'attribute' => 'value'
]);

$treeNode->add("main-node", $childNode);
```

The `add` method returns an instance of the child node (`TreeNode`) that was added, allowing you to create a parent-child relationship and build a hierarchy of items.

### Accessing Child Nodes

To access a specific child node, you can use the `get` method and pass the child reference name as an argument. For example:

```php
$child_node = $treeNode->get("main-node");
```

This will return the child node that is referenced as `"main-node"`. You can traverse multiple levels of nodes using this method. For example:

```php
$treeNode->get('main-node')->get('another-node'); // and so on
```

### Accessing All Child Nodes

`TreeNode` uses the `__get` magic method to retrieve property publicly. Therefore, you can access all direct child nodes using the `children` property.

```php
$treeNode->children; // An array containing all child nodes
```

### Obtaining Parent Node

You can obtain the parent node of a child node by accessing the `parentNode` property:

```php
$child_node->parentNode; // The parent TreeNode
```

This code retrieves the `TreeNode` instance of the parent node.

### Removing Nodes

The `remove` method allows you to remove a child `TreeNode` object from its parent. You can remove a node by providing its name:

```php
$treeNode->remove("main-node"); // remove "main-node" and all its children
```

The `remove` method provides a way to dynamically remove specific nodes from the hierarchy when needed.

### Managing Attributes

TreeNode allows you to manage attributes associated with nodes:

- To retrieve the value of a specific attribute, you can use the `getAttr` method:

  ```php
  $main_node->getAttr('label'); // returns "dashboard" (if exists)
  ```

- To set the value of an attribute, you can use the `setAttr` method:

  ```php
  $main_node->setAttr('label', "profile"); // overwrite or add new label attribute
  $main_node->setAttr('title', "Node Title"); // create a new attribute named "title"
  ```

- To remove an attribute from a node, you can use the `removeAttr` method:

  ```php
  $main_node->removeAttr('label'); // label attribute removed
  ```

## Node Structure Demonstration:

Here's an example of how TreeNode can be used to create a dynamic node with a parent-child hierarchy:

```php
$treeNode = new TreeNode();
	
$treeNode->add("node-ref", [
    "label" => "Home",
    "href" => "//example.com"
]);

$nodeChild = $treeNode->get("node-ref")->add("node-child", [
    "label" => "About",
    "href" => "//example.com/about"
]);

$innerNode = $nodeChild->add("inner-node", [
    "label" => "Services",
    "href" => "//example.com/services"
]);

// ... etc
```

The above example demonstrates how you can create a hierarchical node structure using TreeNode.

## Iterating the Node Structure

To iterate the node structure, you can define a **self-recursive** function that traverses the `TreeNode` object and generates the desired output.\
Here's an example of a self-recursive function called `iterator`:

```php
function iterator(TreeNode $node) {
    
    if ($node->level > 5) {
        throw new Exception("You should not append node beyond the depth of 5 levels");
    }
    
    $href = $node->getAttr('href');
    $label = $node->getAttr('label');

    $html = "<li>";
    $html .= "<a href='" . $href . "'>" . $label . "</a>";

    if (count($node->children) > 0) {

        $html .= "<ul class='child'>";

        foreach ($node->children as $childNode) {
            // Recursive Iteration
            $html .= iterator($childNode);
        }

        $html .= "</ul>";

    }

    $html .= "</li>";

    return $html;
    
};

$result = "<ul class='main-nav'>" . iterator($treeNode) . "</ul>";

echo $result;
```

In this example: 

- The `iterator` function takes a `TreeNode` object as its parameter. 
- It checks the level property of the `TreeNode` object to avoid exceeding a certain depth limit. 
- It then generates the HTML markup for the current node item and recursively calls itself (`iterator`) to generate the HTML for the nested nodes.

The resulting HTML structure can be customized to fit your project's design and requirements.

## Versatility of TreeNode

TreeNode's versatility extends beyond creating nodes. It can be used for managing any kind of parent-child hierarchy. Some examples include:

- Organizational charts
- Directory structures
- User hierarchies

With its robust functionality and flexible nature, TreeNode can handle and facilitate various scenarios involving hierarchical data. Its intuitive methods and straightforward implementation make it a valuable tool for projects that require efficient management of hierarchical structures.

## Author

Uchenna Ajah @ Ucscode &lt;uche23mail@gmail.com&gt;

