<?php

namespace Ucscode\UssElement;

use Exception;

/**
 * Uss Element Builder
 *
 * Basically, DOM Elements are build with tags (such as h1, p, ...) and attributes (class=, style=).
 * Then they have children and parent. That's all!
 * Any other component added to DOM are features to make it more awesome.
 * This class focuses on the basics of building tags, adding attributes and assigning it to another element
 */
class UssElement extends AbstractUssElementParser
{
    public function __construct(string $tagName)
    {
        parent::__construct($tagName);
        $this->void = in_array($this->tagName, $this->voidTags);
    }

    /**
     * Indicate whether the element should have a closing tag or not.
     */
    public function setVoid(bool $void): self
    {
        $this->void = $void;
        return $this;
    }

    /**
     * Confirm if element is void
     */
    public function isVoid(): bool
    {
        return $this->void;
    }

    /**
     * Hide element from browser DOM while still available and accessible
     */
    public function setInvisible(bool $status): self
    {
        $this->invisible = $status;
        return $this;
    }

    /**
     * Check if element is hidden in DOM
     */
    public function isInvisible(): bool
    {
        return $this->invisible;
    }

    /**
     * Checks if an attribute exists on the element.
     *
     * @param string $attr The name of the attribute to check.
     * @return bool `true` if the attribute exists, `false` otherwise.
     */
    public function hasAttribute(string $attr): bool
    {
        return isset($this->attributes[$attr]);
    }

    /**
    * Sets the value of an attribute for the HTML element.
    *
    * @param string $attr The name of the attribute to set.
    * @param string|null $value The value to assign to the attribute.
    * @return self
    */
    public function setAttribute(string $attr, ?string $value = null): self
    {
        $attr = $this->evaluate($attr);
        $this->attributes[$attr] = $this->slice($value);
        return $this;
    }

    /**
     * Checks if an attribute has a particular value.
     *
     * @param string $attr The name of the attribute to check.
     * @param string $value The value to check for.
     * @return bool `true` if the attribute has the specified value, `false` otherwise.
     */
    public function hasAttributeValue(string $attr, string $value): bool
    {
        if(!$this->hasAttribute($attr)) {
            return false;
        };
        $value = $this->slice($value);
        foreach($value as $unit) {
            if(!in_array($unit, $this->attributes[$attr])) {
                return false;
            };
        };
        return !empty($value);
    }

    /**
     * Gets the value of an attribute.
     *
     * @param string $attr The name of the attribute to retrieve.
     * @return string|null The value of the specified attribute, or null if the attribute does not exist.
     */
    public function getAttribute(string $attr): ?string
    {
        if($this->hasAttribute($attr)) {
            return implode(" ", $this->attributes[$attr]);
        };
        return null;
    }

    /**
     * Get all element attributes
     *
     * @return array
     */
    public function getAttributes(): array
    {
        $attributes = [];
        foreach($this->attributes as $key => $value) {
            $attributes[$key] = implode(" ", $value);
        }
        return $attributes;
    }

    /**
     * Appends a value to an attribute.
     *
     * @param string $attr The name of the attribute to modify.
     * @param string $value The value to append to the attribute.
     * @return self
     */
    public function addAttributeValue(string $attr, string $value): self
    {
        $attr = $this->evaluate($attr);
        $value = $this->slice($value);
        $merge = array_merge($this->attributes[$attr], $value);
        $this->attributes[$attr] = array_unique($merge);
        return $this;
    }

    /**
     * Removes a value from an attribute.
     *
     * @param string $attr The name of the attribute to modify.
     * @param string $value The value to remove from the attribute.
     * @return self
     */
    public function removeAttributeValue(string $attr, string $value): self
    {
        $attr = $this->evaluate($attr);
        $value = $this->slice($value);
        $diff = array_diff($this->attributes[$attr], $value);
        $this->attributes[$attr] = $diff;
        return $this;
    }

    /**
     * Removes an attribute from the element.
     *
     * @param string $attr The name of the attribute to remove.
     * @return self
     */
    public function removeAttribute(string $attr): self
    {
        if(isset($this->attributes[$attr])) {
            unset($this->attributes[$attr]);
        }
        return $this;
    }

    /**
     * Sets the inner HTML content of the element.
     *
     * @param string $content The HTML content to set.
     * @return self
     */
    public function setContent(?string $content): self
    {
        array_walk($this->children, function ($child) {
            $child->setParent(null);
        });
        $this->children = [];
        $this->content = $content;
        return $this;
    }

    /**
     * Checks if the element has inner HTML content.
     *
     * @return bool `true` if the element has inner HTML content, `false` otherwise.
     */
    public function hasContent(): bool
    {
        return !is_null($this->content);
    }

    /**
     * Gets the inner HTML content of the element.
     *
     * @return string|null The inner HTML content as a string.
     */
    public function getContent(): ?string
    {
        return $this->content;
    }

    /**
     * Get the child elements of the current element
     *
     * @return array Containing child elements
     */
    public function getChildren(): array
    {
        return $this->children;
    }

    /**
     * Appends a child element to the current element.
     *
     * @param UssElement $child The child element to append.
     * @return self
     */
    public function appendChild(UssElement $child): self
    {
        $child = $this->inspectChild($child, __METHOD__);
        $this->children[] = $child;
        return $this;
    }

    /**
     * Prepends a child element to the current element.
     *
     * @param UssElement $child The child element to prepend.
     * @return self
     */
    public function prependChild(UssElement $child): self
    {
        $child = $this->inspectChild($child, __METHOD__);
        array_unshift($this->children, $child);
        return $this;
    }

    /**
     * Inserts a child element before a specified reference element.
     *
     * @param UssElement $child The child element to insert.
     * @param UssElement $reference The reference element before which the child will be inserted.
     * @return void
     */
    public function insertBefore(UssElement $child, UssElement $reference): self
    {
        return $this->insertAtReferenceIndex($child, $reference, 0);
    }

    /**
     * Inserts a child element after a specified reference element.
     *
     * @param UssElement $child The child element to insert.
     * @param UssElement $reference The reference element after which the child will be inserted.
     * @return void
     */
    public function insertAfter(UssElement $child, UssElement $reference): self
    {
        return $this->insertAtReferenceIndex($child, $reference, 1);
    }

    /**
     * Replaces a child element with another element.
     *
     * @param UssElement $child The new child element to replace the reference element.
     * @param UssElement $reference The reference element to be replaced.
     * @return void
     */
    public function replaceChild(UssElement $child, UssElement $reference): self
    {
        $key = array_search($reference, $this->children, true);
        if($key !== false) {
            $child = $this->inspectChild($child, __METHOD__);
            $this->children[$key] = $child;
        }
        return $this;
    }

    /**
     * Returns the first child element of the current element.
     *
     * @return UssElement|null The first child element as a UssElement object, or null if there are no children.
     */
    public function getFirstChild(): ?UssElement
    {
        return $this->children[0] ?? null;
    }

    /**
     * Returns the last child element of the current element.
     *
     * @return UssElement|null The last child element as a UssElement object, or null if there are no children.
     */
    public function getLastChild(): ?UssElement
    {
        $index = count($this->children) - 1;
        return $this->children[$index] ?? null;
    }

    /**
     * Returns a child element at a specified index.
     *
     * @param int $index The index of the child element to retrieve.
     * @return UssElement|null The child element as a UssElement object, or null if the index is out of bounds.
     */
    public function getChild(int $index): ?UssElement
    {
        return $this->children[$index] ?? null;
    }

    /**
     * Removes a child element from the current element.
     *
     * @param UssElement $child The child element to remove.
     * @return void
     */
    public function removeChild(UssElement $child): void
    {
        $key = array_search($child, $this->children, true);
        if($key !== false) {
            unset($this->children[$key]);
            $this->children = array_values($this->children);
        };
    }

    /**
     * Remove all child elements and context from the element
     * @return void
     */
    public function freeElement(): self
    {
        $this->children = [];
        $this->content = null;
        return $this;
    }

    /**
     * Generates an HTML string representation of the element and its children.
     *
     * @param bool $indent If true, the generated HTML will be indented for readability.
     * @return string The HTML string representing the element and its children.
     */
    public function getHTML(bool $indent = false): string
    {
        $index = $indent ? 0 : null;
        $html = $this->buildNode($this, $index);
        return $html;
    }

    /**
     * @method getParentElement
     */
    public function getParentElement(): ?UssElementInterface
    {
        return $this->parentElement;
    }

    /**
     * @method getParentElement
     */
    public function hasParentElement(): bool
    {
        return !empty($this->parentElement);
    }

    /**
     * @method sortChildren
     */
    public function sortChildren(callable $callback): void
    {
        usort($this->children, $callback);
    }

    /**
     * @method openTag
     */
    public function open(): string
    {
        $element = new UssElement($this->nodeName);
        foreach($this->getAttributes() as $key => $value) {
            $element->setAttribute($key, $value);
        }
        return preg_replace("/<\/" . strtolower($this->nodeName) . ">$/", '', $element->getHTML());
    }

    /**
     * @method closeTag
     */
    public function close(): string
    {
        return '</' . strtolower($this->nodeName) . '>';
    }

    /**
     * Set the parent element of the current element
     *
     * @param UssElement $parent
     * @return void
     * @ignore
     */
    protected function setParent(UssElement $parent): void
    {
        $this->parentElement = $parent;
    }

    /**
     * Change Position of element by adding it after or before a reference
     * 
     * @param UssElement $child - The element to move above or below a target
     * @param UssElemnet $reference - The targeted element 
     * @param integer $index - 0 to move before, 1 to move after
     */
    protected function insertAtReferenceIndex(UssElement $child, UssElement $reference, int $index = 0): self
    {
        if(array_search($reference, $this->children, true) !== false) {
            $child = $this->inspectChild($child, __METHOD__);
            $key = array_search($reference, $this->children, true);
            array_splice($this->children, ($key + $index), 0, [$child]);
            $this->children = array_values($this->children);
        }
        return $this;
    }

    /**
     * @ignore
     */
    private function evaluate(string $attr)
    {
        $attr = str_replace(" ", '', $attr);
        if(!isset($this->attributes[$attr])) {
            $this->attributes[$attr] = [];
        };
        return $attr;
    }

    /**
     * @ignore
     */
    private function inspectChild(UssElement $child, string $method): UssElement
    {
        if($this === $child) {
            throw new Exception(
                sprintf(
                    "Trying to add self as child in %s", 
                    $method
                )
            );
        };

        $key = array_search($child, $this->children, true);

        if($key !== false) {
            array_splice($this->children, $key, 1);
            $this->children = array_values($this->children);
        };

        $child->setParent($this);
        return $child;
    }
}
