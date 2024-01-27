<?php

namespace Uss\Component\Kernel\Interface;

use mysqli_result;

interface UssInterface extends UssFrameworkInterface
{
    /**
     * Render A Twig Template
     *
     * @param string    $templateFile   Reference to the twig template.
     * @param array     $variables:     A list of variables that will be passed to the template
     * @param bool      $return         Whether to return or print the output
     */
    public function render(string $templatePath, array $variables, bool $return = false): ?string;

    /**
    * Terminate the script and print a JSON response.
    *
    * @param bool|int|null $status   The status of the response.
    * @param string|null   $message  The optional message associated with the response.
    * @param mixed         $data     Additional data to include in the response.
    */
    public function terminate(bool|int|null $status, ?string $message, mixed $data = []): void;

    /**
     * Converts a mysqli_result object to an associative array.
     *
     * @param mysqli_result     $result     The mysqli_result object to convert.
     * @param callable|null     $mapper     An optional callback function to apply to each row before adding it to the result.
     *                                      The callback should accept a value and a key as its arguments.
     *
     * @return array The resulting associative array.
     */
    public function mysqliResultToArray(mysqli_result $result, ?callable $mapper = null): array;

    /**
     * Get the availabe columns of a table
     *
     * This method scans a table in the database schema and return all available columns associated with the table
     *
     * @param string $tableName: The name of the table to retrive the columns
     *
     * @return array: A list of all the columns
     */
    public function getTableColumns(string $tableName): array;

    /**
     * Implode an array into a readable string using a specified binder.
     *
     * @param array|null    $array      The array to be imploded.
     * @param string|null   $binder     The string used to join array elements. Defaults to 'and'.
     *
     * @return string       The imploded string.
     */
    public function implodeReadable(?array $array, ?string $binder = 'and'): string;

    /**
     * Fetch a single item from a database table based on specified conditions.
     *
     * @param string        $table      The name of the database table.
     * @param string|array  $value      The value or array of values to match against the specified column.
     * @param string        $column     The column to match against when a single value is provided.
     *                                  Defaults to 'id'.
     *
     * @return array|null   An associative array representing the fetched item, or null if no matching item is found.
     */
    public function fetchItem(string $table, string|array $value, $column = 'id'): ?array;

    /**
     * Sanitize data to prevent security vulnerabilities.
     *
     * @param mixed     $data The data or array of data to be sanitized.
     * @param bool      $sqlEscape Whether to perform SQL escaping. Defaults to false.
     *
     * @return mixed    The sanitized data.
     */
    public function sanitize(mixed $data, bool $sqlEscape = false): mixed;

    /**
     * Applies a callback recursively to all elements of the given array.
     *
     * @param callable $callback The callback function to apply to each element.
     *                           The callback should accept a single parameter and return the modified value.
     * @param array    $array    The input array to be mapped recursively.
     *
     * @return array    The resulting array after applying the callback to each element recursively.
     */
    public function array_map_recursive(callable $callback, array $array): array;

    /**
     * Replace Variables in a String
     *
     * Replaces variables in the given string with corresponding values from the data array.
     * The variables in the string must be in the format **%\\{variable_name}**.
     *
     * @param string $string The string containing variables to replace.
     * @param array $data An associative array with variable-value pairs.
     *
     * @return string The modified string with variables replaced by their values.
     */
    public function replaceVars(string $data, array $vars): string;
}
