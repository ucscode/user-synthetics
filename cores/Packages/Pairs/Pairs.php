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
     * Return all data in collective pattern
     *
     * @param int|null|string $ref - The reference ID of sequences to retrieve. 
     * use `Pairs::ALL` to get all values
     * @param string|null $like - A LIKE expression pattern to filter returned values
     * 
     * @return array
     */
    public function getSequence(null|int|string $ref = self::ALL, ?string $like = null): array
    {
        $sequence = [];

        $squery = (new SQuery())
            ->select()
            ->from($this->table)
            ->orderBy(['_ref', '_key']);

        if(!empty($like)) {
            $squery->getWhereCondition()
                ->add("_key", '%' . $like . '%', 'LIKE');
        }

        $result = $this->mysqli->query($squery->build());
        
        if($result->num_rows) {
            while($item = $result->fetch_assoc()) 
            {
                $offset = $item['_ref'] ?? '';
                $index = $item['_key'];
                $sequence[$offset] ??= [];

                $sequence[$offset][$index] = [
                    'value' => json_decode($item['_value'], true),
                    'epoch' => (int)$item['epoch']
                ];
            }
        }
        
        return $ref === self::ALL ? $sequence : ($sequence[$ref ?? ''] ?? []);
    }
}
