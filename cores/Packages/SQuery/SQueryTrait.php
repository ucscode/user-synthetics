<?php

namespace Ucscode\SQuery;

trait SQueryTrait
{
    protected function tick(string $entity): string
    {
        $entity = array_map(function($value) {
            if(preg_match("/^\w+$/i", $value)) {
                return $this->surround($value, "`");
            };
        }, explode(".", trim($entity)));
        return implode(".", $entity);
    }

    protected function surround(string $value, string $char): string
    {
        $pattern = sprintf('/^%s.*%s$/', $char, $char);
        if (!preg_match($pattern, $value)) {
            $value = $char . $value . $char;
        }
        return $value;
    }
}
