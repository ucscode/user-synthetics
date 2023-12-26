<?php

namespace Ucscode\Pairs;

use Ucscode\SQuery\Condition;
use Ucscode\SQuery\SQuery;

/**
 * A Meta Data Storage System
 *
 * @author ucscode
 * @see sQuery https://github.com/ucscode/sQuery
 * @link https://github.com/ucscode/pairs
 */
class Pairs extends AbstractPairs
{
    /**
     * Add or update a reference data
     *
     * @param string $key The key of the reference data.
     * @param mixed $value The value of the reference data.
     * @param int|null $ref The ID of the reference data. Defaults to `null`.
     * 
     * @return bool
     */
    public function set(string $key, mixed $value, ?int $ref = null): bool
    {
        $data = [
            '_key' => $this->mysqli->real_escape_string($key),
            '_value' => $this->mysqli->real_escape_string(json_encode($value)),
            '_ref' => $ref,
        ];
        
        $SQL = (new SQuery())
            ->select()
            ->from($this->table)
            ->where(
                (new Condition())
                    ->add("_key", $data['_key'])
                    ->and("_ref", $data['_ref'])
            )
            ->build();
        
        $exists = $this->mysqli->query($SQL)->num_rows;
        
        if(!$exists) {
            $squery = (new SQuery())->insert($this->table, $data);
        } else {
            $squery = (new SQuery())
                ->update($this->table, $data)
                ->where(
                    (new Condition())
                        ->add("_key", $data['_key'])
                        ->and("_ref", $data['_ref'])
                );
        };
        
        $SQL = $squery->build();
        return $this->mysqli->query($SQL);
    }

    /**
     * Retrieve a reference data or epoch by key and reference ID
     *
     * @param string $key The key of the reference data to retrieve.
     * @param int|null $ref The ID of the reference data. Defaults to `null`.
     * @param bool $epoch return the timestamp when data was inserted.
     */
    public function get(string $key, ?int $ref = null, bool $epoch = false): mixed
    {
        $SQL = (new SQuery())->select()
            ->from($this->table)
            ->where(
                (new Condition())
                    ->add("_key", $key)
                    ->and("_ref", $ref)
            )
            ->build();
        
        $result = $this->mysqli->query($SQL)->fetch_assoc();

        if($result) {
            $offset = $epoch ? 'epoch' : '_value';
            $value = json_decode($result[$offset], true);
            return $value;
        }

        return null;
    }

    /**
     * Remove reference data by key and reference ID
     *
     * @param string $key The key of the reference data to remove.
     * @param int|null $ref The ID of the reference data. Defaults to null.
     * 
     * @return bool
     */
    public function remove(string $key, ?int $ref = null): bool
    {
        $SQL = (new SQuery())
            ->delete()
            ->from($this->table)
            ->where(
                (new Condition())
                    ->add("_key", $key)
                    ->and("_ref", $ref)
            )
            ->build();
        
        $result = $this->mysqli->query($SQL);
        return $result;
    }

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
            ->from($this->table);

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
        $group = [];

        if($result->num_rows) {

            while($pair = $result->fetch_assoc()) {

                $refId = $pair['_ref'];
                $refId = is_numeric($refId) ? (int)$refId : null;

                // Create group for each reference
                if(!isset($group[$refId])) {
                    $group[$refId] = array();
                };

                $key = $pair['_key'];
                $value = json_decode($pair['_value'], true);

                $group[$refId][$key] = $value;

            };

        };

        return ($ref === false) ? $group : ($group[$ref] ?? []);

    }
}
