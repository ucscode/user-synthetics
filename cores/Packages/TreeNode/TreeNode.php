<?php

namespace Ucscode\TreeNode;

use Exception;

class TreeNode
{
    public readonly int $index;
    public readonly ?string $name;
    protected string $identity;
    protected ?int $level = 0;
    protected array $children = [];
    protected array $attributes = [];
    protected ?TreeNode $parent = null;
    private static int $lastIndex = 0;

    /**
     * Creates a new instance of the TreeNode class.
     *
     * @param string|null $name The name of the node. Defaults to the class name if not provided.
     * @param array $attributes The attributes of the node, specified as an associative array.
     */
    public function __construct(?string $name = null, array $attributes = [])
    {
        $this->index = self::$lastIndex;
        $this->identity = $this::class . '::' . (!$this->index ? 'ROOT' : 'INDEX_' . $this->index);
        $this->name = $name;
        array_walk($attributes, fn ($value, $key) => $this->setAttribute($key, $value));
        self::$lastIndex++;
    }

    /**
     * Get level of current node relative the root node
     */
    public function getLevel(): int
    {
        return $this->level;
    }

    /**
     * Get name of current node
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get unique index of current node
     */
    public function getIndex(): int
    {
        return $this->index;
    }

    /**
     * Get unique identity of current node
     */
    public function getIdentity(): string
    {
        return $this->identity;
    }

    /**
     * Adds a child node to the current TreeNode object.
     *
     * @param string $name The name of the child node.
     * @param array $attrs The attributes of the child node, specified as an associative array.
     * @return TreeNode The newly created child node.
     */
    public function addChild(string $name, array|TreeNode $component = []): TreeNode
    {
        if (!empty($this->children[$name])) {
            throw new Exception("Duplicate Child: '{$name}' already added to '{$this->name}'");
        }

        $child = ($component instanceof TreeNode) ? $component : new self($name, $component);
        $child->parent = $this;
        $child->level = $this->level + 1;

        $this->synchronizeChildren(
            $child->children,
            fn ($child) => $child->level = ($child->parent->level + 1)
        );

        return $this->children[$name] = $child;
    }


    /**
     * Retrieves a child node from the current TreeNode object.
     *
     * @param string $name The name of the child node to retrieve.
     * @return ?TreeNode The child node or `null` if not found.
     */
    public function getChild(string $name): ?TreeNode
    {
        return $this->children[$name] ?? null;
    }

    /**
     * Removes a child node from the current TreeNode object.
     *
     * @param string $name The name of the child node to remove.
     * @return TreeNode The removed node or `null` if not found
     */
    public function removeChild(string $name): ?TreeNode
    {
        $child = $this->children[$name] ?? null;
        if(!empty($child)) {
            unset($this->children[$name]);
        }
        return $child;
    }

    /**
     * Retrieve the children of the current node
     */
    public function getChildren(): array
    {
        return $this->children;
    }

    /**
     * Sorts the children of the TreeNode using a custom comparison function.
     *
     * @param callable $func A callback function for custom sorting.second.
     */
    public function sortChildren(callable $func): void
    {
        usort($this->children, $func);
    }

    /**
     * Find a child node by index
     */
    public function findIndexChild(int $index): ?TreeNode
    {
        return $this->synchronizeChildren($this->children, function ($child) use ($index) {
            if($child->index === $index) {
                return $child;
            }
        });
    }

    /**
     * Get the parent node of the current node
     */
    public function getParent(): ?TreeNode
    {
        return $this->parent;
    }

    /**
     * Sets an attribute for the current TreeNode object.
     *
     * @param string $name The name of the attribute to set.
     * @param mixed $value The value to assign to the attribute.
     */
    public function setAttribute(string $name, mixed $value): self
    {
        $this->attributes[$name] = $value;
        return $this;
    }

    /**
     * Retrieves the value of a specified attribute from the current TreeNode object.
     *
     * @param string $name The name of the attribute to retrieve.
     * @return mixed The value of the attribute, or `null` if the attribute does not exist.
     */
    public function getAttribute(string $name): mixed
    {
        return $this->attributes[$name] ?? null;
    }

    /**
     * Removes a specified attribute from the current TreeNode object.
     *
     * @param string $name The name of the attribute to remove.
     * @return bool `true` if the attribute was successfully removed, `false` otherwise.
     */
    public function removeAttribute(string $name): self
    {
        if(isset($this->attributes[$name])) {
            unset($this->attributes[$name]);
        }
        return $this;
    }

    /**
     * Retrieves all attributes from the current TreeNode object.
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * Recursively processes an array of children using a callback function.
     *
     * @param array $children The array of children to process.
     * @param callable $process The callback function to apply to each child.
     *                          This function should take a child as a parameter and return a value.
     *                          If the function returns false for a child, the current iteration is skipped and the loop continues with the next child.
     *                          If the function returns null for a child, the recursion continues with the current child's children.
     *                          If the function returns any other value for a child, the recursion stops and that value is returned.
     *
     * @return mixed The value returned by the callback function for a child, if any.
     *               If the callback function never returns a non-null, non-false value, this method returns null.
     */
    public function synchronizeChildren(array $children, callable $process): mixed
    {
        foreach($children as $child) {
            if(($value = $process($child)) === false) {
                continue; // Continue to the next child
            }
            $value ??= $this->synchronizeChildren($child->children, $process);
            if($value !== null) {
                return $value; // Return the value and stop processing
            }
        };
        return null;
    }

    /**
     * Debug Information
     *
     * Render the most relevant node information for debugging purpose
     */
    public function __debuginfo(): array
    {
        $debugInfo = [
            '::identity' => $this->identity,
            'name' => $this->name,
            'index' => $this->index,
            'level' => $this->level,
        ];

        $optional = [
            'attributes' => $this->attributes,
            'children' => $this->children,
        ];

        foreach($optional as $name => $value) {
            if(!empty($this->{$name}) || $this->level) {
                $debugInfo[$name] = $value;
            }
        };

        $parentName = $this->parent->name ?? 'NULL';
        $debugInfo['::parent'] = !$this->parent ? null : $this->parent->identity . " ({$parentName})";

        return $debugInfo;
    }
}
