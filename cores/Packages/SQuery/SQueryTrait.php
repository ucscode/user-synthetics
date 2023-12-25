<?php

namespace Ucscode\SQuery;

trait SQueryTrait
{
    /**
     * Surround an SQL column or reserved word with backtick
     *
     * @param string $entity - The column or reserved word to backtick
     */
    protected function tick(string $entity): string
    {
        $entity = array_map(function ($value) {
            if(preg_match("/^\w+$/i", $value)) {
                $value = $this->surround($value, "`");
            };
            return $value;
        }, explode(".", trim($entity)));
        return implode(".", $entity);
    }

    /**
     * Surround any text with the specified character
     *
     * @param string $value - The text to be surrounded
     * @param string $char - The character used to surround the text
     */
    protected function surround(string $value, string $char): string
    {
        $pattern = sprintf('/^%s.*%s$/', $char, $char);
        $wrapped = preg_match($pattern, $value);
        if($this->mysqli && $char === "'" && !$wrapped) {
            $value = $this->mysqli->real_escape_string($value);
        }
        return !$wrapped ? ($char . $value . $char) : $value;
    }
}
