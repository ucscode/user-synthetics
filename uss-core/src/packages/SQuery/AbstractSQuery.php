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

    /**
     * Use SQuery Object as a String
     */
    public function __toString()
    {
        return $this->getQuery();
    }

    /**
     * Reset the SQL container
     * @return self
     */
    public function clear(): self
    {
        $this->SQL = [];
        $const = $this->getClassConstants('SECTION_');
        foreach($const as $key => $const) {
            $this->SQL[$const] = [];
        };
        return $this;
    }

    /**
     * Get the SQL query string after it has been constructed.
     *
     * This method retrieves the fully constructed SQL query string that has been built using the methods and parameters
     * of this class. It provides access to the SQL query for further use or debugging.
     *
     * @return string The SQL query string.
     */
    public function getQuery(): string
    {
        $SQLSET = $this->SQL;
        $SQL = [];

        // INSERT STATEMENT
        if($this->isType(self::TYPE_INSERT)) {

            $SQL = $SQLSET[self::SECTION_INSERT];
            $SQL[] = "(" . implode(", ", array_keys($SQLSET[self::SECTION_VALUES])) . ")";
            $SQL[] = "VALUES";
            $SQL[] = "(" . implode(", ", array_values($SQLSET[self::SECTION_VALUES])) . ")";

            // UPDATE STATEMENT
        } elseif($this->isType(self::TYPE_UPDATE)) {

            $SQL = $SQLSET[self::SECTION_UPDATE];
            $combination = [];

            foreach($SQLSET[self::SECTION_VALUES] as $key => $value) {
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

    /**
     * Check the type of query being built.
     *
     * This method checks whether the query being constructed is of the specified type,
     * which can be one of SELECT, INSERT, UPDATE, or DELETE.
     *
     * @param string $type The query type to check (e.g., SELECT, INSERT, UPDATE, DELETE).
     *
     * @return bool True if the query being built is of the specified type, otherwise false.
     */
    protected function isType(string $type): bool
    {
        if(!in_array($type, $this->getClassConstants('TYPE_'))) {
            return false;
        }
        return !empty($this->SQL[$type]);
    }

    /**
     * Ensure that the SQL builder container is either refreshed or represents a new instance.
     *
     * This method is responsible for ensuring that the SQL builder container is in a favorable state
     * for usage.
     *
     * @throws Exception if the builder container has been previously occupied.
     *
     * @return void
     */
    protected function demandForNewQuery(string $method): void
    {
        $query = $this->getQuery();
        if(!empty($query)) {
            throw new \Exception('Current Instance already occupied. You should create a new instance for the ' .  $method . '() method');
        }
    }

    /**
     * Enclose column names with backticks to ensure proper SQL formatting.
     *
     * This method takes a column name as input and ensures that it is enclosed with backticks (`) to ensure
     * proper formatting for SQL queries. It handles column names that may include table aliases and aliases
     * that are not already enclosed in backticks.
     *
     * @param string $column The column name to be enclosed with backticks.
     *
     * @return string The column name enclosed with backticks.
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
     * Add a new condition to the SQL query.
     *
     * This method is used to construct and add a new condition to the SQL query being built. Conditions consist
     * of keywords (e.g., WHERE, HAVING), keys (column or expression names), values, operators (e.g., '=', '<', 'LIKE'),
     * and terms indicating whether keys and values should be treated as columns/variables or data types.
     *
     * @param string $keyword The keyword representing the type of condition (e.g., WHERE, HAVING).
     * @param string $key The key (column or expression) for the condition.
     * @param mixed $value The value to compare or use in the condition.
     * @param string|null $operator The operator for comparing the key and value (optional, defaults to null).
     * @param int $keyTerm Indicates how to treat the key:
     * @param int $valueTerm Indicates how to treat the value:
     * @param string|null $sqlIndex An optional index to define the position of the generated query.
     *
     * @return string|null The generated query condition if no $sqlIndex is provided, otherwise, it returns null.
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

    /**
    * Modify a value based on the specified term.
    *
    * This method takes a value and a term as input and applies a modification to the value based on the
    * specified term. The term can be one of the predefined constants:
    *
    * @param mixed $value The value to be modified.
    * @param int $term The term that specifies the modification to be applied
    *
    * @return mixed The modified value based on the specified term.
    */
    protected function isolate($value, $term): mixed
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
