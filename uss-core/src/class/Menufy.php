<?php

/**
 * Menufy - Menu Tree Generator
 *
 * The Menufy class is a parent-children hierarchy tree generator that facilitates the creation and management
 * of menu structures. It provides a flexible and dynamic way to create menus, add unlimited children,
 * change parent relationships, and update menu links.
 *
 * ### Example
 * ```php
 * $menufy = new Menufy();
 *
 * // Create the main menu
 * $mainMenu = $menufy->add('main-menu', [
 *     'label' => 'Home',
 *     'href' => '//example.com'
 * ]);
 *
 * // Add child menu to the main menu
 * $childMenu1 = $mainMenu->add('child-menu-1', [
 *     'label' => 'About',
 *     'href' => '//example.com/about'
 * ]);
 *
 * // Add child menu to menu 1
 * $childMenu2 = $childMenu1->add('child-menu-2', [
 *     'label' => 'FAQ',
 *     'href' => '//example.com/FAQ'
 * ]);
 * ```
 *
 * ***
 *
 * > To utilize the Menufy instance effectively, you need to create a recursive function that can parse the nodes in the menufy object and generate the desired tree structure.
 * This recursive function will iterate through the nodes, retrieve their attributes, and handle any child nodes present.
 * By implementing a recursive approach, you can handle menus with unlimited depth, ensuring all levels of the hierarchy are processed correctly.
 *
 * ***
 *
 * Menufy is a versatile class that provides functionality for creating and managing hierarchical structures,
 * primarily designed for menus. However, it can also be used for various other purposes involving hierarchical data.
 *
 * Here are a few examples of how the Menufy class can be utilized beyond menu creation:
 *
 * 1. **Nested Lists:** Menufy can be used to generate nested lists, such as hierarchical directory structures,
 *    table of contents, or any other nested list-based data representation.
 *
 * 2. **Site Maps:** Menufy can assist in creating site maps for websites, allowing you to generate a hierarchical
 *    representation of your site's pages and their relationships.
 *
 * 3. **Breadcrumbs:** Breadcrumbs are often used in navigation to show the user's current location within a website's
 *    hierarchy. Menufy can help generate breadcrumbs by representing the hierarchical structure and rendering
 *    the path from the root to the current page.
 *
 * 4. **Tree Views:** If you need to display hierarchical data in a tree-like structure, such as organizational charts,
 *    category trees, or file systems, Menufy can be used to create and manage the tree view.
 *
 * 5. **Dynamic Navigation:** Menufy enables you to dynamically manage and update navigation menus on your website
 *    based on various conditions, such as user roles, permissions, or other dynamic factors. You can modify
 *    the menu structure and attributes based on your specific requirements.
 *
 * 6. **Content Organization:** Menufy can be used to organize and manage content in a hierarchical manner. This can be
 *    helpful for creating and managing complex content structures, such as multi-level categories, tags, or sections.
 *
 * These are just a few examples of how the Menufy class can be utilized beyond menu creation. Its flexibility and
 * hierarchical management capabilities make it a versatile tool for various applications involving hierarchical data
 * structures.
 *
 * ---
 *
 * @category   Utility
 * @package    Menufy
 * @version    2.0.6
 * @author     Your Name
 * @link       https://github.com/ucscode/menufy
 */
class Menufy
{
    /**
     * The name of the Menufy object
     *
     * @var string
     */
    protected $name;

    /**
     * The parent of the Menufy object
     *
     * `null` signifies that the object is the root node
     *
     * @var null|Menufy
     */
    private $parentMenu = null;

    /**
     * The depth or level of the Menufy object
     *
     * @var null|int
     */
    protected $level = null;

    /**
     * The children of the Menufy object
     *
     * An array containing multiple Menufy objects representing the Menufy children
     *
     * @var array
     */
    protected $child = [];

    /**
     * The attributes of the Menufy object
     *
     * The storage box of Menufy object
     *
     * @var array
     */
    protected $attrs = [];


    /**
     * Menufy Constructor
     *
     * Creates a new instance of the Menufy class.
     *
     * @param string|null $name The name of the menu. Defaults to the class name if not provided.
     * @param array $attrs The attributes of the menu, specified as an associative array.
     * @return void
     */
    public function __construct(?string $name = null, array $attrs = [])
    {

        /** Menu Name */
        $this->name = $name ?? __CLASS__;

        /** Set Menu Attributes */
        foreach($attrs as $key => $value) {
            $this->setAttr($key, $value);
        }

    }


    /**
     * Add Child Node
     *
     * Adds a child node to the current Menufy object.
     *
     * @param string $name The name of the child node.
     * @param array $attrs The attributes of the child node, specified as an associative array.
     * @return self The newly created child Menufy object.
     * @throws \Exception If a child node with the same name already exists in the current Menufy object.
     */
    public function add(string $name, array $attrs = [])
    {

        $_class = __CLASS__;

        /* Avoid Duplicate Child Name */

        if(!empty($this->child[ $name ])) {
            throw new \Exception("($_class) Duplicate Child: {`{$name}`} already added to {`{$this->name}`}");
        }

        /** Create New Menufy Object */

        $child = new self($name, $attrs);
        $child->level = is_null($this->level) ? 0 : ($this->level + 1);
        $child->parentMenu = $this;

        return $this->child[ $name ] = $child;

    }


    /**
     * Get Child Node
     *
     * Retrieves a child node from the current Menufy object based on its name.
     *
     * @param string $name The name of the child node to retrieve.
     * @return self|null The child Menufy object if found, or `null` if not found.
     */
    public function get(string $name)
    {
        return $this->child[ $name ] ?? null;
    }

    /**
     * Remove Child Node
     *
     * Removes a child node from the current Menufy object based on its name.
     *
     * @param string $name The name of the child node to remove.
     * @return self The updated Menufy object after the child node has been removed.
     */
    public function remove(string $name)
    {
        if(isset($this->child[ $name ])) {
            unset($this->child[ $name ]);
        }
        return $this;
    }


    /**
     * Set Attribute
     *
     * Sets an attribute for the current Menufy object.
     *
     * @param string $name The name of the attribute to set.
     * @param mixed $value The value to assign to the attribute.
     * @return bool Returns `true` if the attribute was successfully set, `false` otherwise.
     */
    public function setAttr(string $name, $value)
    {
        $this->attrs[ $name ] = $value;
        return isset($this->attrs[ $name ]);
    }

    /**
     * Get Attribute
     *
     * Retrieves the value of a specified attribute from the current Menufy object.
     *
     * @param string $name The name of the attribute to retrieve.
     * @return mixed|null The value of the attribute, or `null` if the attribute does not exist.
     */
    public function getAttr(string $name)
    {
        return $this->attrs[ $name ] ?? null;
    }

    /**
     * Remove Attribute
     *
     * Removes a specified attribute from the current Menufy object.
     *
     * @param string $name The name of the attribute to remove.
     * @return bool `true` if the attribute was successfully removed, `false` otherwise.
     */
    public function removeAttr(string $name)
    {
        if(isset($this->attrs[ $name ])) {
            unset($this->attrs[ $name ]);
        }
        return !isset($this->attrs[ $name ]);
    }

    /**
     * Magic Setter Method
     *
     * Throws an exception if a property is attempted to be set directly. Use the `set_attr()` method instead.
     *
     * @param string $key The name of the property.
     * @param mixed $value The value to set.
     * @throws \Exception Throws an exception indicating that properties should not be set directly.
     * @ignore
     */
    public function __set($key, $value)
    {
        $_class = __CLASS__;
        throw new \Exception("Do not set '{$_class}::\${$key}' property directly. Use the {$_class}::set_attr() method");
    }


    /**
     * Magic Getter Method
     *
     * Retrieves the value of a property.
     *
     * @param string $key The name of the property.
     * @return mixed|null The value of the property, or `null` if the property does not exist.
     * @ignore
     */
    public function __get($key)
    {
        return $this->{$key} ?? null;
    }

    /**
     * Magic Isset Method
     *
     * Checks if a property is set.
     *
     * @param string $var The name of the property.
     * @return bool `true` if the property is set and not empty, `false` otherwise.
     * @ignore
     */
    public function __isset($var)
    {
        return !empty($this->{$var});
    }

    /**
     * As children nodes are being appended
     * The debug information becomes to confusing
     * Most of the information are also not required at the point of debugging
     * Hence, we need to limit the information returned
     *
     * @ignore
     */
    /*
    public function __debuginfo() {

        global $MenufyClassDebugLevel;

        if( is_null($MenufyClassDebugLevel) ) $MenufyClassDebugLevel = -1;

        $MenufyClassDebugLevel++;

        $data = array();

        //if( $MenufyClassDebugLevel ) return $data;

        foreach( (new \ReflectionClass($this))->getProperties() as $ReflectionProperty ) {

            if( $ReflectionProperty->isPrivate() ) $visual = "private";
            else if( $ReflectionProperty->isProtected() ) $visual = "protected";
            else $visual = "public";

            $name = $ReflectionProperty->name;
            $value = $this->{$name};

            $data["{$name}:{$visual}"] = $value;

        };

        return $data;

    }
    */

}
