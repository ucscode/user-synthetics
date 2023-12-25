<?php

namespace Ucscode\SQuery;

use mysqli;

class Condition
{
    use SQueryTrait;

    protected array $condition = [];

    public function __construct(protected ?mysqli $mysqli = null)
    {}

    /**
     * Add a new condition
     *
     * @param string $key - The table column to query
     * @param string|array|null $value - The value to test against the column
     * @param ?string $operand - The operator to use (such as =, >, REGEXP, LIKE etc)
     * @param ?bool $delimiter - The character to wrap values.
     *  - false (default): wrap in single quote.
     *  - true: wrap in backtick
     *  - null: Do not wrap the value
     */
    public function add(string $key, null|string|int|float|array $value, ?string $operand = null, ?bool $delimeter = false): self
    {
        return $this->and($key, $value, $operand, $delimeter);
    }

    /**
     * Same as add
     */
    public function and(string $key, null|string|int|float|array $value, ?string $operand = null, ?bool $delimeter = false): self
    {
        return $this->setFilter($key, $value, $operand, empty($this->condition) ? null : 'AND', $delimeter);
    }

    /**
     * Same as add except that it uses 'OR' instead of 'AND'
     */
    public function or(string $key, null|string|int|float|array $value, ?string $operand = null, ?bool $delimeter = false): self
    {
        return $this->setFilter($key, $value, $operand, empty($this->condition) ? null : 'OR', $delimeter);
    }

    /**
     * Add a custom condition
     *
     * @param string $filter - The custom condition to be added
     */
    public function customFilter(string $filter): self
    {
        $this->condition[] = $filter;
        return $this;
    }

    /**
     * Build the condition result
     *
     * @param string $keyword - The prefix that will be added (such as WHERE or HAVING) when building the condition.
     */
    public function build(?string $keyword = null): ?string
    {
        if(!empty($this->condition)) {
            $condition = $keyword . ' ' .implode("\n\t", $this->condition);
            return trim($condition);
        }
        return null;
    }

    protected function setFilter(string $key, null|string|int|float|array $value, ?string $operand, ?string $prefix, ?bool $delimeter): self
    {
        $key = $this->tick($key);

        switch(strtoupper(gettype($value))) {

            case 'ARRAY':
                $value = array_map(function ($unit) use ($delimeter) {
                    return $this->wrapValue($unit, $delimeter);
                }, array_values($value));
                $value = "(" . implode(", ", $value) . ")";
                $operand = "IN";
                break;

            case 'NULL':
                $value = 'NULL';
                $operand ??= 'IS';
                break;

            default:
                $operand ??= '=';
                $value = $this->wrapValue($value, $delimeter);
        }

        $this->condition[] = trim("{$prefix} {$key} {$operand} {$value}");
        return $this;
    }

    protected function wrapValue(mixed $value, ?bool $delimeter): mixed
    {
        if(!is_null($delimeter)) {
            $value = is_bool($value) ? (int)$value : $value;
            if(!in_array(gettype($value), ['integer', 'float', 'double'])) {
                $value = $delimeter ? $this->tick($value) : $this->surround($value, "'");
            }
        }
        return $value;
    }
}
