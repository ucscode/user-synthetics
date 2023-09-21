<?php

namespace Ucscode\SQuery;

abstract class AbstractSQuery implements SQueryInterface
{
    /** @ignore */
    protected array $SQL = [];

    public function __construct()
    {
        $this->clear();
    }

    public function __toString()
    {
        return $this->getQuery();
    }

    public function clear(): self {
        $this->SQL = [];
        $const = $this->getClassConstants('SECTION_');
        foreach($const as $key => $const) {
            $this->SQL[$const] = [];
        };
        return $this;
    }

    public function getQuery() {
        $SQLSET = $this->SQL;
        $SQL = [];

        // INSERT STATEMENT
        if($this->isType(self::TYPE_INSERT)) {

            $SQL = $SQLSET[self::SECTION_INSERT];
            $SQL[] = "(" . implode(", ", $SQLSET[self::SECTION_COLUMNS]) . ")";
            $SQL[] = "VALUES";
            $SQL[] = "(" . implode(", ", $SQLSET[self::SECTION_VALUES]) . ")";

            // UPDATE STATEMENT
        } elseif($this->isType(self::TYPE_UPDATE)) {

            $SQL = $SQLSET[self::SECTION_UPDATE];
            $SQL[] = "SET";

            $combination = array_combine(
                $SQLSET[self::SECTION_COLUMNS],
                $SQLSET[self::SECTION_VALUES]
            );

            foreach($combination as $key => $value) {
                $combination[$key] = "{$key} = {$value}";
            };

            $SQL[] = implode(",\n", array_values($combination));

            $SQL = $this->importSection(self::SECTION_WHERE, $SQL);

            // DELETE STATEMENT
        } elseif($this->isType(self::TYPE_DELETE)) {

            $SQL = $SQLSET[self::SECTION_DELETE];
            $SQL = $this->importSection(self::SECTION_WHERE, $SQL);

            // SELECT STATEMENT
        } else {
            $SQLSET = array_filter($SQLSET);
            foreach($SQLSET as $section => $queries) {
                foreach($queries as $query) {
                    $SQL[] = trim($query);
                }
            };
        }

        return implode("\n", $SQL);
    }

    protected function isType(string $type): bool
    {
        if(!in_array($type, $this->getClassConstants('TYPE_'))) {
            return false;
        }
        return !empty($this->SQL[$type]);
    }

    protected function demandForNewQuery(string $method)
    {
        $query = $this->getQuery();
        if(!empty($query)) {
            throw new \Exception('Current Instance already occupied. You should create a new instance for the ' .  $method . '() method');
        }
    }

    /**
     * Format the column name by applying backticks if needed.
     *
     * This method takes a column name as input and checks if it requires formatting with backticks.
     * @ignore
     */
    protected function backtick(string $column): string
    {
        $explodedValue = explode('.', $column);
        $division = array_map(function ($column) {
            if(!preg_match('/^\*|`.*`$/', $column)) {
                $column = explode(" ", $column);
                if(!is_numeric($column[0])) {
                    $column[0] = "`{$column[0]}`";
                };
                $column = implode(" ", $column);
            };
            return $column;
        }, $explodedValue);
        $column = implode(".", $division);
        return $column;
    }

    /**
     * Generate an aliased column syntax that follows SQL conventions.
     *
     * This method takes a column syntax as input and generates an aliased column syntax that adheres to SQL conventions.
     * @ignore
     */
    protected function alias_column(string $column): string
    {
        $column = trim($column);
        $expr = "(?:\w+|`\w+`|\*)";
        $alias = preg_split("/\s(?:as)\s/i", $column);

        array_walk($alias, function (&$identifier, $key) use ($expr) {
            $is_column_name = preg_match("/^{$expr}(?:\.{$expr})?$/i", $identifier);
            if($is_column_name) {
                $identifier = $this->backtick($identifier);
            }
            return $identifier;
        });

        $alias = implode(' AS ', $alias);
        return $alias;
    }

    /**
     * Add a new condition
     */
    protected function addCondition(
        string $keyword,
        string $key,
        mixed $value,
        ?string $operator,
        int $keyTerm,
        int $valueTerm,
        ?string $sqlIndex = null
    ) {

        $query = "{$keyword} ";

        $query .= $this->refactor($key, $keyTerm) . ' ';

        if(is_array($value)) {

            $value = $this->refactor($value, $valueTerm);
            $query .= "IN(" . implode(", ", $value) . ")";

        } elseif($value === self::IS_NULL) {

            $query .= 'IS NULL';

        } elseif($value === self::IS_NOT_NULL) {

            $query .= 'IS NOT NULL';

        } elseif($value !== null) {

            $value = $this->refactor($value, $valueTerm);

            if($operator === null) {
                $operator = '=';
            };

            $operator = trim($operator);
            $query .= $operator . " " . $value;

        };

        $query = trim($query);

        if($sqlIndex) {
            $this->SQL[$sqlIndex][] = $query;
        } else {
            return $query;
        };

    }

    protected function refactor(mixed $value, int $term = self::FILTER_QUOTE)
    {

        if(is_bool($value)) {

            // If expression is boolean, return 1 or 0
            return $value ? 1 : 0;

        } elseif(is_int($value) || is_float($value)) {

            // if expression is a number, return the number
            return $value;

            // Normal
        } elseif(is_scalar($value)) {

            return $this->isolate($value, $term);

        } elseif(is_array($value)) {

            $value = array_map(function ($value) use ($term) {
                if(!is_scalar($value)) {
                    return null;
                } else {
                    return $this->refactor($value, $term);
                };
            }, array_values($value));

            return array_filter($value);

        }

    }

    /**
     * Format the input by applying single quote if needed.
     *
     * This method takes an input and checks if it requires formatting with single quote.
     * If the input is not already enclosed in backticks, it adds backticks to ensure proper formatting.
     * The method supports column names with or without table aliases, handling them correctly.
     *
     * @param string $input The input to format.
     * @return string The formatted output.
     * @ignore
     */
    protected function quote(?string $input): string
    {
        $pattern = '/^(["\'])(.*)\\1$/';
        $quoted = preg_match($pattern, $input);
        if(is_null($input)) {
            $input = 'NULL';
        } elseif(!$quoted) {
            $input = "'" . $input . "'";
        };
        return $input;
    }

    protected function isolate($value, $term)
    {
        if($term === self::FILTER_BACKTICK) {
            $value = $this->backtick($value);
        } elseif($term === self::FILTER_QUOTE) {
            $value = $this->quote($value);
        }
        return $value;
    }

    protected function hasKeyword(string $keyword, string $index)
    {
        return !empty(preg_grep("/^{$keyword}/", $this->SQL[$index]));
    }

    protected function getClassConstants(?string $prefix = null)
    {
        $prefix = strtoupper($prefix);
        $reflectionClass = new \ReflectionClass(self::class);
        $constants = $reflectionClass->getConstants();
        if(!empty($prefix)) {
            $constants = array_filter($constants, function ($value, $key) use ($prefix) {
                return preg_match("/^{$prefix}/", $key);
            }, ARRAY_FILTER_USE_BOTH);
        };
        return $constants;
    }

    protected function importSection(string $section, array $SQL)
    {
        $section = $this->SQL[$section] ?? [];
        foreach($section as $value) {
            $SQL[] = $value;
        }
        return $SQL;
    }

}
