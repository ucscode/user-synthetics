<?php

namespace Uss\Component\Kernel\Interface;

use mysqli_result;

interface UssInterface
{
    public const NAMESPACE = 'Uss';
    public const SESSION_KEY = 'UssId'; // (PHP Session)
    public const CLIENT_KEY = 'uss_client_id'; // (Browser Cookie)

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
}
