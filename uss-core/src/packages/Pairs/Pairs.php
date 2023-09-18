<?php

namespace Ucscode\Packages;

use Ucscode\SQuery\SQuery;

/**
 * A Meta Data Storage System
 *
 * The Pairs class represents a utility for managing a meta table that stores key-value pairs.
 * It provides methods to create the meta table, link it to a parent table, add or update reference data,
 * retrieve reference data based on keys and reference IDs, remove reference data, and retrieve all data
 * associated with a specific reference ID or matching a certain pattern.
 *
 * The class relies on the `sQuery` class to execute SQL queries. It requires a valid instance of the `MYSQLI` class
 * and the name of the meta table to work with.
 *
 * ### Usage example:
 * ```php
 * $mysqli = new mysqli('localhost', 'username', 'password', 'database');
 * $pairs = new Pairs($mysqli, 'meta_table');
 * $pairs->set('key1', 'value1', 1); // Add or update reference data with key 'key1' and reference ID 1
 * $data = $pairs->get('key1', 1); // Retrieve the value associated with key 'key1' and reference ID 1
 * $pairs->remove('key1', 1); // Remove the reference data with key 'key1' and reference ID 1
 * ```
 *
 * @package pairs
 * @author ucscode
 * @see sQuery https://github.com/ucscode/sQuery
 * @link https://github.com/ucscode/pairs
 */
class Pairs
{
    /** @ignore */
    private $tablename;

    /** @ignore */
    private $mysqli;

    /**
     * Constructor Method
     *
     * Initializes a new instance of the Pairs class.
     * It requires an instance of the `MYSQLI` class and the name of the meta table to work with.
     *
     * - Throws an exception if the required `sQuery` class is not found.
     * - Automatically creates the meta table if it doesn't already exist.
     *
     * @param MYSQLI $mysqli An instance of the MYSQLI class for database connection.
     * @param string $tablename The name of the meta table.
     *
     * @throws \Exception If the `sQuery` class is not found.
     *
     * @return void
     */
    public function __construct(\MYSQLI $mysqli, string $tablename)
    {
        if(!class_exists(SQuery::class)) {
            throw new \Exception(__CLASS__ . "::__construct() relies on class `sQuery` to operate");
        }

        $this->tablename = $tablename;
        $this->mysqli = $mysqli;

        $this->createTable();
    }

    /**
     * Link to parent table
     *
     * Applies a foreign key constraint to the `_ref` column of the meta table. Thus, referencing the parent table.
     *
     * @param string $parent_table The name of the parent table.
     * @param string $constraint The unique constraint name for the foreign key.
     * @param string $primary_key The primary key column of the parent table. Default column is assumed to be `id`.
     * @param string $action The action to take on delete (CASCADE, RESTRICT, SET NULL). Default is 'CASCADE'.
     * @return bool Returns `true` if the foreign key constraint is added or already exists, `false` otherwise.
     */
    public function linkParentTable(string $parent_table, string $constraint, string $primary_key = 'id', string $action = 'CASCADE')
    {
        $SQL = "
			IF NOT EXISTS (
				SELECT NULL 
				FROM information_schema.TABLE_CONSTRAINTS
				WHERE
					CONSTRAINT_SCHEMA = DATABASE() AND
					CONSTRAINT_NAME   = '{$constraint}' AND
					CONSTRAINT_TYPE   = 'FOREIGN KEY' AND
					TABLE_NAME = '{$this->tablename}'
			)
			THEN
				ALTER TABLE `{$this->tablename}`
				MODIFY `_ref` INT NOT NULL,
				ADD CONSTRAINT `{$constraint}`
				FOREIGN KEY (`_ref`)
				REFERENCES `{$parent_table}`(`{$primary_key}`)
				ON DELETE {$action};
			END IF
		";

        return $this->mysqli->query($SQL);
    }

    /**
     * Add or update a reference data
     *
     * This method adds a new reference data if it does not exist. Otherwise, it updates the existing reference data.
     * The reference data is considered unique if **both** the key and the reference ID exist and do not match any other key/reference ID in the table.
     *
     * The value of the reference data can be of any type, as it will be encoded into `JSON` format before being stored in the database.
     *
     * Note that if an `object` is passed as the value, it will be returned as an `array` when retrieved.
     * It is important to ensure that the value being passed does not require additional escaping, as the method already applies the necessary escaping using `real_escape_string()`.
     *
     * @param string $key The key of the reference data.
     * @param mixed $value The value of the reference data.
     * @param int|null $ref The ID of the reference data. Defaults to `null`.
     * @return mixed Returns the result of the query execution. That is, `true` on success, `false` on failure.
     */
    public function set(string $key, $value, ?int $ref = null)
    {
        $value = json_encode($value);
        $value = $this->mysqli->real_escape_string($value);

        $SQL = (new SQuery())
            ->select()
            ->from($this->tablename)
            ->where("_key", $key)
            ->and("_ref", $this->valueOf($ref));

        $data = [
            "_key" => $key,
            "_value" => $value,
            "_ref" => $ref
        ];

        if(!$this->mysqli->query($SQL)->num_rows) {
            $SQL->clear()
                ->insert($this->tablename, $data);
        } else {
            $SQL->clear()
                ->update($this->tablename, $data)
                ->where("_key", $key)
                ->and("_ref", $this->valueOf($ref));
        };

        $result = $this->mysqli->query($SQL);
        return $result;
    }

    /**
     * Retrieve a reference data by key and reference ID
     *
     * This method retrieves a reference data from the table based on the provided key and reference ID.
     * If the reference data exists, it is returns. Otherwise, it returns `null`.
     *
     * If the third parameter is set to `true`, the unix timestamp at which the data was
     * inserted will be returned instead of the value.
     *
     * @param string $key The key of the reference data to retrieve.
     * @param int|null $ref The ID of the reference data. Defaults to `null`.
     * @param bool $epoch Determines whether to retrieve the reference data or a unix timestamp which indicates the date of insertion. Defaults to `false`.
     * @return mixed|null Returns the reference data as an associative array if found. Returns null if no matching reference data is found.
     */
    public function get(string $key, ?int $ref = null, bool $epoch = false)
    {
        $Query = (new SQuery())->select()
            ->from($this->tablename)
            ->where("_key", $key) 
            ->and("_ref", $this->valueOf($ref));

        $result = $this->mysqli->query($Query)->fetch_assoc();

        if($result) {
            $value = json_decode($result[ $epoch ? 'epoch' : '_value' ], true);
            return $value;
        }
    }

    /**
     * Remove reference data by key and reference ID
     *
     * This method removes the reference data from the table based on the provided key and reference ID.
     * If the reference data matching the key and reference ID is found, it will be deleted from the table.
     *
     * @param string $key The key of the reference data to remove.
     * @param int|null $ref The ID of the reference data. Defaults to null.
     * @return bool Returns `true` if the reference data is successfully removed. Returns `false` otherwise.
     */
    public function remove(string $key, ?int $ref = null)
    {
        $SQL = (new SQuery())->delete($this->tablename)
            ->where("_key", $key)
            ->and("_ref", $this->valueOf($ref));

        $result = $this->mysqli->query($SQL);
        return $result;
    }

    /**
     * Return all the data that matches a reference id (or/and) a particular pattern
     *
     * ##### Example:
     * If the meta table is used to store additional detail of a set registered users, then you can get all the data associated to a particular user by passing only the reference id of the user.
     *
     * You can also pass `regular expression` string as the second argument to get only values that matches a particular key.
     *
     * Note: Regular expression should not begin with a delimeter. By default, all expressions are case insensitive
     *
     * ```php
     * $pairs->get(1, "/^wallet[\w+]$/i"); // Wrong
     * $pairs->get(1, "^wallet[\w+]$"); // Right
     * ```
     *
     * @param int|null $ref The reference id
     * @param string|null $regex A regular expression of matching keys
     *
     * @return mixed
     *
     */

    /**
     * Return all the data that matches a reference id (or/and) a particular pattern
     *
     * This method retrieves all reference data from the table that matches the specified reference ID and regular expression pattern (optional).
     *
     * If a reference ID is provided, any array containing reference data of the matching reference ID will be retrieved.
     * If a regular expression pattern is provided, only reference data with matching keys matching will be retrieved.
     *
     * @param int|null $ref The ID of the reference data to retrieve. Defaults to `null`.
     * @param string|null $regex The regular expression pattern to match against the keys. Defaults to `null`.
     * @return array An associative array containing the retrieved reference data. The keys of the array represent the reference data keys,
     * and the values can be of mixed types including strings, numbers, or arrays.
     */
    public function all($ref = null, ?string $regex = null)
    {
        // Check if argument 1 is given;
        if(!empty(func_get_args())) {

            $ref = func_get_arg(0);

            if(!is_null($ref)) {

                if(is_numeric($ref)) {

                    $ref = (int)$ref;

                } else {

                    $type = gettype($ref);
                    $backtrace = debug_backtrace();

                    $callerPath = $backtrace[0]['file'];
                    $errorLine = $backtrace[0]['line'];

                    $error = __METHOD__ . "(): Argument #1 (\$ref) must be of type ?int, {$type} given, called in {$callerPath} on line {$errorLine}";

                    throw new \TypeError($error);

                };

            };

        } else {
            $ref = false;
        }

        // Prepare Query;
        $SQL = (new SQuery())->select()
            ->from($this->tablename);

        // Prepare Reference;
        if($ref === false) {
            $SQL->where(1);
        } else {
            $SQL->where("_ref", $this->valueOf($ref));
        }

        // Prepare Regular Expression;
        if(!empty($regex)) {
            $regex = str_replace("\\", "\\\\", $regex);
            $SQL->and("_key", $regex, 'REGEXP');
        };

        // Order Query
        $SQL->orderBy('_ref');

        # Execute Query;
        $result = $this->mysqli->query($SQL);

        if($result->num_rows) {

            while($pair = $result->fetch_assoc()) {

                $refId = $pair['_ref'];
                $refId = is_numeric($refId) ? (int)$refId : null;

                if(!isset($group[$refId])) {
                    $group[$refId] = array();
                };

                $key = $pair['_key'];
                $value = json_decode($pair['_value'], true);

                $group[$refId][$key] = $value;

            };

        };

        return ($ref === false) ? $group : $group[$ref];

    }

    /**
     * Create a meta table
     *
     * Creates a meta table in the database if it doesn't already exist.
     *
     * @return bool Returns `true` if the table creation query is successful, `false` otherwise.
     */
    protected function createTable()
    {

        $SQL = "
			CREATE TABLE IF NOT EXISTS `{$this->tablename}` (
				`id` INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
				`_ref` INT,
				`_key` VARCHAR(255) NOT NULL,
				`_value` TEXT,
				`epoch` BIGINT NOT NULL DEFAULT UNIX_TIMESTAMP()
			);
		";

        return $this->mysqli->query($SQL);

    }

    /**
     * Check comparism type
     *
     * Determines the comparison type based on the provided reference ID.
     *
     * @param int|null $ref The reference ID to check.
     * @return string Returns the comparison string for the reference ID. If the reference ID is null, it returns 'IS NULL', otherwise ' = [reference ID]'.
     * @ignore
     */
    private function valueOf(?int $ref = null)
    {
        return is_null($ref) ? SQuery::IS_NULL : $ref;
    }

}
