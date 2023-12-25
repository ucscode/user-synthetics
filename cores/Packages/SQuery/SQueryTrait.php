<?php

namespace Ucscode\SQuery;

trait SQueryTrait
{
    /**
     * Backtick a string representing an SQL column or reserved word
     * 
     * @param string $entity - The column or reserved word to backtick
     */
    protected function tick(string $entity): string
    {
        $entity = array_map(function($value) {
            if(preg_match("/^\w+$/i", $value)) {
                return $this->surround($value, "`");
            };
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
        if (!preg_match($pattern, $value)) {
            $value = $char . $value . $char;
        }
        return $value;
    }
}
