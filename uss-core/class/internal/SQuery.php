<?php
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
class SQuery
{
    /**
     * Format the column name by applying backticks if needed.
     *
     * This method takes a column name as input and checks if it requires formatting with backticks.
     * If the column name does not start with '*' or is not already enclosed in backticks, it adds backticks to ensure proper formatting.
     * The method supports column names with or without table aliases, handling them correctly.
     *
     * @param string|null $column The column name to format.
     * @return string|null The formatted column name, or null if the input is null.
     * @ignore
     */
    private static function backtick_data(?string $column)
    {
        if ($column) {
            $division = array_map(function ($column) {
                return !preg_match('/^\*|`.*`$/', $column) ? "`$column`" : $column;
            }, explode('.', $column));
            $column = implode(".", $division);
        };
        return $column;
    }

    /**
     * Generate an aliased column syntax that follows SQL conventions.
     *
     * This method takes a column syntax as input and generates an aliased column syntax that adheres to SQL conventions.
     * It trims the input column syntax and splits it by the "AS" keyword, preserving the aliases if present.
     * Each part of the column syntax is checked against a regular expression pattern to ensure it matches the SQL convention.
     * If a part is a valid column name, it is formatted using the `backtick_data` method to apply backticks if necessary.
     * Finally, the parts are joined back together using " AS " as the separator, and the generated aliased column syntax is returned.
     *
     * @param string $column The column syntax to generate an alias for.
     * @return string The aliased column syntax following SQL conventions.
     * @ignore
     */

    private static function alias_column(string $column)
    {

        $column = trim($column);
        $expr = "(?:\w+|`\w+`|\*)";

        $alias = preg_split("/\s(?:as)\s/i", $column);

        array_walk($alias, function (&$identifier, $key) use ($expr) {
            $is_column_name = preg_match("/^{$expr}(?:\.{$expr})?$/i", $identifier);
            if($is_column_name) {
                $identifier = self::backtick_data($identifier);
            }
            return $identifier;
        });

        $alias = implode(' AS ', $alias);

        return $alias;

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
     * @return string The generated SELECT SQL statement.
     */
    public static function select(string $tablename, ?string $condition = null, $columns = '*')
    {

        if(is_null($condition)) {
            $condition = 1;
        }

        if(!is_array($columns)) {
            $columns = array($columns);
        }

        $columns = implode(', ', array_map(function ($column) {
            return self::alias_column($column);
        }, $columns));

        $tablename = self::backtick_data($tablename);

        $SQL = "SELECT {$columns} FROM {$tablename} WHERE {$condition}";

        return $SQL;
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
    public static function val($value)
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
     * @return string The generated INSERT SQL statement.
     */
    public static function insert(string $tablename, array $data)
    {

        $columns = implode(", ", array_map(function ($key) {
            return self::backtick_data($key);
        }, array_keys($data)));

        $values = array_map(function ($value) {
            return self::val($value);
        }, array_values($data));

        $values = implode(", ", $values);

        $tablename = self::backtick_data($tablename);

        $SQL = "INSERT INTO {$tablename} ($columns) VALUES ($values)";

        return $SQL;
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
     * @return string The generated UPDATE SQL statement.
     */
    public static function update(string $tablename, array $data, ?string $condition)
    {

        $tablename = self::backtick_data($tablename);

        $fieldset = array_map(function ($key, $value) {
            return self::backtick_data($key) . " = " . self::val($value);
        }, array_keys($data), array_values($data));

        $fieldset = implode(", ", $fieldset);

        if(is_null($condition)) {
            $condition = 1;
        }

        $SQL = "UPDATE {$tablename} SET {$fieldset} WHERE {$condition}";

        return $SQL;

    }

    /**
     * Generate a DELETE SQL statement.
     *
     * This method constructs a DELETE SQL statement to delete rows from the specified table based on the provided condition.
     *
     * @param string $tablename The name of the table from which to delete rows.
     * @param string $condition The condition for the WHERE clause. Specifies the rows to be deleted based on the condition.
     * @return string The generated DELETE SQL statement.
     */
    public static function delete(string $tablename, string $condition)
    {

        $tablename = self::backtick_data($tablename);

        $SQL = "DELETE FROM {$tablename} WHERE {$condition}";

        return $SQL;

    }

}
