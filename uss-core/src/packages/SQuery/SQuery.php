<?php

namespace Ucscode\SQuery;

/**
 * A simple SQL query builder class that provides methods for generating SQL statements.
 *
 * This stand-alone class offers a set of static methods to generate commonly used SQL statements such as `SELECT`, `INSERT`, `UPDATE`, and `DELETE`.
 * It handles proper formatting of column names, values, and aliases, ensuring compatibility with SQL conventions.
 * The class aims to simplify the process of constructing SQL queries in PHP applications.
 *
 * @package squery
 * @author ucscode
 * @link https://github.com/ucscode/sQuery
 */
class SQuery extends AbstractSQuery
{
    use SQueryTrait {

        deriveFilterCondition as public where;
        deriveFilterCondition as public and;
        deriveFilterCondition as public or;

        deriveInfluenceCondition as public orderBy;
        deriveInfluenceCondition as public having;

        mergeTable as public from;
        mergeTable as public leftJoin;
        mergeTable as public rightJoin;
        mergeTable as public innerJoin;
        mergeTable as public fulljoin;
        mergeTable as public crossJoin;

    }

    /**
     * Generate a SELECT SQL statement.
     *
     * This method constructs a SELECT SQL statement based on the provided table name, condition, and columns.
     * The table name and columns are properly formatted to ensure SQL convention compliance.
     *
     * If no condition is specified, a default condition of `1` is used to select all rows.
     * The third parameter accepts either a comma-separated string or an array of column names to be selected.
     *
     * @param string $tablename The name of the table.
     * @param string|null $condition The condition for the WHERE clause. Defaults to `1` if not specified.
     * @param string|array|null $columns The columns to select. Defaults to `*` if not specified.
     * @return self The generated SELECT SQL statement.
     */
    public function select(string|array $columns = '*', ?string $from = null): self
    {
        $this->demandForNewQuery(__METHOD__);

        if(!is_array($columns)) {
            $columns = explode(',', $columns);
        };

        $columns = implode(', ', array_map(function ($column) {
            return $this->alias_column($column);
        }, $columns));

        $this->SQL[self::SECTION_SELECT][] = "SELECT {$columns}";

        if($from !== null) {
            $this->from($from);
        };

        return $this;
    }

    public function groupBy(string|array $values) {
        if(gettype($values) === 'string') {
            $values = array_map('trim', explode(",", $values));
        };
        $values = $this->refactor($values, self::FILTER_BACKTICK);
        $query = 'GROUP BY ' . implode(", ", $values);
        $this->SQL[self::SECTION_GROUP_BY][] = $query;
        return $this;
    }

    public function on(
        string $key,
        mixed $value = null,
        ?string $operator = null,
        int $keyTerm = self::FILTER_QUOTE,
        int $valueTerm = self::FILTER_QUOTE
    ) {
        $query = $this->addCondition('', $key, $value, $operator, $keyTerm, $valueTerm);
        $query = "ON (" . $query . ")";
        $this->SQL[self::SECTION_JOIN][] = $query;
        return $this;
    }

    public function limit(int $from, int $max = null): self
    {
        if($max === null) {
            $max = $from;
            $from = 0;
        }
        $this->SQL['LIMIT'][] = "LIMIT {$from}, {$max}";
        return $this;
    }

    public function raw(string $query, string $index = self::SECTION_WHERE): self
    {
        $this->SQL[$index][] = $query;
        return $this;
    }

    /**
     * Format a value for use in an SQL statement.
     *
     * This method takes a value as input and formats it to be used in an SQL statement.
     * If the value is `null`, it returns the string **'NULL'**.
     * Otherwise, it wraps the value in single quotes ('') to ensure proper SQL syntax.
     *
     * @param mixed $value The value to format.
     * @return string The formatted value for use in an SQL statement.
     * @ignore
     */
    public function val($value): string
    {
        if (is_null($value)) {
            return 'NULL';
        } else {
            return "'{$value}'";
        }
    }

    /**
     * Generate an INSERT SQL statement.
     *
     * This method constructs an INSERT SQL statement to insert data into the specified table.
     * The method takes a table name and an associative array representing the data to be inserted,
     * where the array keys represent column names and the values represent the corresponding values to be inserted.
     *
     * @param string $tablename The name of the table.
     * @param array $data The data to be inserted.
     * @return self The generated INSERT SQL statement.
     */
    public function insert(string $tablename, array $data = []): self
    {
        $this->demandForNewQuery(__METHOD__);

        $tablename = $this->backtick($tablename);

        $this->SQL[self::SECTION_INSERT][] = "INSERT INTO {$tablename}";

        foreach($data as $key => $value) {
            $this->set($key, $value);
        }

        return $this;
    }

    public function set(string $key, ?string $value, int $valueTerm = self::FILTER_QUOTE): self
    {

        // Only INSERT && UPDATE can use this method;

        if($this->isType(self::TYPE_INSERT) || $this->isType(self::TYPE_UPDATE)) {

            $key = $this->backtick($key);

            $this->SQL[self::SECTION_COLUMNS][] = $key;
            $this->SQL[self::SECTION_VALUES][] = $this->isolate($value, $valueTerm);

        };

        return $this;
    }

    /**
     * Generate an UPDATE SQL statement.
     *
     * Constructs an UPDATE SQL statement to update data in the specified table.
     * The method takes a table name, an associative array representing the data to be updated, and an optional condition for the WHERE clause.
     *
     * @param string $tablename The name of the table.
     * @param array $data The data to be updated. The array keys represent column names, and the values represent the corresponding values to be updated.
     * @param string|null $condition The condition for the WHERE clause. If provided, it filters the rows to be updated. If `null`, a default condition of `1` will be used, updating all rows.
     * @return self The generated UPDATE SQL statement.
     */
    public function update(string $tablename, array $data = []): self
    {
        $this->demandForNewQuery(__METHOD__);

        $tablename = $this->backtick($tablename);

        $this->SQL[self::SECTION_UPDATE][] = "UPDATE {$tablename}";

        foreach($data as $key => $value) {
            $this->set($key, $value);
        }

        return $this;

    }

    /**
     * Generate a DELETE SQL statement.
     *
     * This method constructs a DELETE SQL statement to delete rows from the specified table based on the provided condition.
     *
     * @param string $tablename The name of the table from which to delete rows.
     * @param string $condition The condition for the WHERE clause. Specifies the rows to be deleted based on the condition.
     * @return self The generated DELETE SQL statement.
     */
    public function delete(string $tablename): self
    {
        $this->demandForNewQuery(__METHOD__);
        $tablename = $this->backtick($tablename);
        $this->SQL[self::SECTION_DELETE][] = "DELETE FROM {$tablename}";
        return $this;
    }

}