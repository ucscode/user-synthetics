<?php

namespace Ucscode\TreeNode;

class TreeNode
{
    /**
     * The ID assigned to the current node
     */
    protected int $nodeId;

    /**
     * The name of the TreeNode object
     */
    protected string $name;

    /**
     * The depth or level of the TreeNode object
     */
    protected ?int $level = 0;

    /**
     * The attributes of the TreeNode object
     */
    protected array $attrs = [];

    /**
     * The children of the TreeNode object
     *
     * An array containing multiple TreeNode objects representing the TreeNode children
     */
    protected array $children = [];

    /**
     * The parent of the TreeNode object
     */
    private ?TreeNode $parentNode = null;
    
    /**
     * Holds incrementing record of node ID
     */
    private static int $lastId = 0;

    /**
     * TreeNode Constructor
     *
     * Creates a new instance of the TreeNode class.
     *
     * @param string|null $name The name of the node. Defaults to the class name if not provided.
     * @param array $attrs The attributes of the node, specified as an associative array.
     * @return void
     */
    public function __construct(?string $name = null, array $attrs = [])
    {
        $this->nodeId = self::$lastId;
        $this->name = $name ?? (__CLASS__ . "\\" . $this->nodeId);
        foreach($attrs as $key => $value) {
            $this->setAttr($key, $value);
        }
        self::$lastId++;
    }


    /**
     * Add Child Node
     *
     * Adds a child node to the current TreeNode object.
     *
     * @param string $name The name of the child node.
     * @param array $attrs The attributes of the child node, specified as an associative array.
     * @return TreeNode The newly created child TreeNode object.
     * @throws \Exception If a child node with the same name already exists in the current TreeNode object.
     */
    public function add(string $name, array|TreeNode $node_or_attrs = []): TreeNode
    {
        if(!empty($this->children[$name])) {
            $_class = __CLASS__;
            throw new \Exception("($_class) Duplicate Child: {`{$name}`} already added to {`{$this->name}`}");
        }

        if($node_or_attrs instanceof TreeNode) {
            $child = $node_or_attrs;
        } else {
            $child = new self($name, $node_or_attrs);
        };

        $child->parentNode = $this;
        $child->level = $this->level + 1;

        $this->updateChildren($child, $child->children);

        return $this->children[$name] = $child;
    }


    /**
     * Get Child Node
     *
     * Retrieves a child node from the current TreeNode object based on its name.
     *
     * @param string $name The name of the child node to retrieve.
     * @return self|null The child TreeNode object if found, or `null` if not found.
     */
    public function get(string $name): ?TreeNode
    {
        return $this->children[$name] ?? null;
    }

    /**
     * Remove Child Node
     *
     * Removes a child node from the current TreeNode object based on its name.
     *
     * @param string $name The name of the child node to remove.
     * @return self The updated TreeNode object after the child node has been removed.
     */
    public function remove(string $name): ?TreeNode
    {
        $child = $this->children[$name] ?? null;
        if(!empty($child)) {
            unset($this->children[$name]);
        }
        return $child;
    }


    /**
     * Set Attribute
     *
     * Sets an attribute for the current TreeNode object.
     *
     * @param string $name The name of the attribute to set.
     * @param mixed $value The value to assign to the attribute.
     * @return bool Returns `true` if the attribute was successfully set, `false` otherwise.
     */
    public function setAttr(string $name, $value): bool
    {
        $this->attrs[$name] = $value;
        return isset($this->attrs[$name]);
    }

    /**
     * Get Attribute
     *
     * Retrieves the value of a specified attribute from the current TreeNode object.
     *
     * @param string $name The name of the attribute to retrieve.
     * @return mixed|null The value of the attribute, or `null` if the attribute does not exist.
     */
    public function getAttr(string $name): mixed
    {
        return $this->attrs[$name] ?? null;
    }

    /**
     * Sort The children based on "order" attribute
     */
    public function sortChildren(callable $func)
    {
        usort($this->children, $func);
    }

    /**
     * Remove Attribute
     *
     * Removes a specified attribute from the current TreeNode object.
     *
     * @param string $name The name of the attribute to remove.
     * @return bool `true` if the attribute was successfully removed, `false` otherwise.
     */
    public function removeAttr(string $name): bool
    {
        if(isset($this->attrs[$name])) {
            unset($this->attrs[$name]);
        }
        return !isset($this->attrs[ $name ]);
    }

    /**
     * Throws an exception if a property is attempted to be set directly.
     * Recommended to use the `setAttr()` method instead.
     *
     * @param string $key The name of the property.
     * @param mixed $value The value to set.
     * @throws \Exception Throws an exception indicating that properties should not be set directly.
     * @ignore
     */
    // public function __set($key, $value)
    // {
    //     $_class = __CLASS__;
    //     throw new \Exception("{$_class}: Not allowed to set `\${$key}` property directly; Use `set_attr()` method instead");
    // }


    /**
     * Magic Getter Method
     *
     * Retrieves the value of a property.
     *
     * @param string $key The name of the property.
     * @return mixed|null The value of the property, or `null` if the property does not exist.
     * @ignore
     */
    public function &__get($key)
    {
        $reference = $this->{$key} ?? null;
        return $reference;
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
     * Debug Information
     *
     * Render the most relevant node information for debugging purpose
     */
    public function __debuginfo()
    {

        $parentInfo = null;

        if($this->parentNode) {
            $parentInfo = $this->parentNode::class . " => (";
            $info = [
                'nodeId' => $this->parentNode->nodeId,
                'name' => $this->parentNode->name,
                'level' => $this->parentNode->level ?? 'null',
                'children' => count($this->parentNode->children),
                'attributes' => count($this->parentNode->attrs)
            ];
            foreach($info as $key => $value) {
                $info[$key] = "{$key}: {$value}";
            };
            $parentInfo .= implode(", ", $info);
            $parentInfo .= ")";
        }

        $debugger = [];

        foreach((new \ReflectionClass($this))->getProperties() as $property) {
            if(!$property->isStatic()) {
                $name = $property->getName();
                $value = match($name) {
                    'parentNode' => $parentInfo,
                    default => $this->{$name}
                };
                if($this->parentNode === null) {
                    if(empty($value) && $name != 'children') {
                        continue;
                    }
                }
                $debugger[$name] = $value;
            }
        };

        return $debugger;

    }

    private function updateChildren(TreeNode $parent, array $children)
    {
        foreach($children as $child) {
            $child->level = $parent->level + 1;
            if(!empty($child->children)) {
                $this->updateChildren($child, $child->children);
            }
        }
    }

}
