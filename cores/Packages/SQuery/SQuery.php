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
     * @param string $tablename The name of the table.
     * @param string|null $from A quick alternative to using "from()" method without alias
     *
     * @return self
     */
    public function select(string|array $columns = '*', ?string $from = null): self
    {
        $this->demandForNewQuery(self::TYPE_SELECT, __METHOD__);

        if(!is_array($columns)) {
            $columns = explode(',', $columns);
        };

        $columns = implode(', ', array_map(function ($column) {
            return $this->alias_column($column);
        }, $columns));

        $this->SQL[self::SECTION_SELECT][0] = "SELECT {$columns}";

        if($from !== null) {
            $this->from($from);
        };

        return $this;
    }

    /**
     * Generate an INSERT SQL statement.
     *
     * @param string $tablename The name of the table.
     * @param array $data The data to be inserted.
     *
     * @return self.
     */
    public function insert(string $tablename, array $data = []): self
    {
        $this->demandForNewQuery(self::TYPE_INSERT, __METHOD__);

        $tablename = $this->backtick($tablename);

        $this->SQL[self::SECTION_INSERT][0] = "INSERT INTO {$tablename}";

        foreach($data as $key => $value) {
            $this->set($key, $value);
        }

        return $this;
    }

    /**
     * Generate an UPDATE SQL statement.
     *
     * @param string $tablename The name of the table.
     * @param array $data The data to be updated. The array keys represent column names, and the values represent the corresponding values to be updated.
     *
     * @return self
     */
    public function update(string $tablename, array $data = []): self
    {
        $this->demandForNewQuery(self::TYPE_UPDATE, __METHOD__);

        $tablename = $this->backtick($tablename);

        $this->SQL[self::SECTION_UPDATE][0] = "UPDATE {$tablename} SET";

        foreach($data as $key => $value) {
            $this->set($key, $value);
        }

        return $this;

    }

    /**
     * Generate a DELETE SQL statement.
     *
     * @param string $tablename The name of the table from which to delete rows.
     *
     * @return self
     */
    public function delete(?string $tablename = null): self
    {
        $this->demandForNewQuery(self::TYPE_DELETE, __METHOD__);
        $this->SQL[self::SECTION_DELETE][0] = "DELETE";
        if(!is_null($tablename)) {
            $tablename = $this->backtick($tablename);
            $this->SQL[self::SECTION_FROM][] = "FROM {$tablename}";
        }
        return $this;
    }

    /**
     * Add a 'GROUP BY' clause to group table set by certain columns
     *
     * @param array|string A comma separated string or an array of table columns
     *
     * @return self
     */
    public function groupBy(string|array $values)
    {
        if(gettype($values) === 'string') {
            $values = array_map('trim', explode(",", $values));
        };
        $values = $this->refactor($values, self::FILTER_BACKTICK);
        $query = 'GROUP BY ' . implode(", ", $values);
        $this->SQL[self::SECTION_GROUP_BY][] = $query;
        return $this;
    }

    /**
     * Add a "LIMIT" clause to limit number of table outputs
     *
     * @param int $from The starting point for the query result (inclusive).
     * @param int|null $max The maximum number of rows to retrieve (optional).
     *
     * @return self
     */
    public function limit(int $from, ?int $max = null): self
    {
        if($max === null) {
            $max = $from;
            $from = 0;
        }
        $this->SQL['LIMIT'] = ["LIMIT {$from}, {$max}"];
        return $this;
    }

    /**
     * Apply a condition for merging tables in a query.
     *
     * This method allows you to specify a condition for merging two tables in a database query.
     *
     * @param string $key The column or expression for the first table
     * @param string $value The column or expression for the second table
     * @param string|null $operator The operator for comparing both table columns
     * @param int $keyTerm Whether to treat the first parameter as a column/variable or datatype:[string, number]
     * @param int $valueTerm Whether to treat the second parameter as a column/variable or datatype:[string, number]
     */
    public function on(
        string $key,
        string $value,
        ?string $operator = null,
        int $keyTerm = self::FILTER_BACKTICK,
        int $valueTerm = self::FILTER_BACKTICK
    ) {
        $query = $this->addCondition('', $key, $value, $operator, $keyTerm, $valueTerm);
        $query = "ON (" . $query . ")";
        $this->SQL[self::SECTION_JOIN][] = $query;
        return $this;
    }

    /**
     * Insert a raw SQL syntax into any section
     *
     * @param string $query The SQL syntax to insert
     * @param string $index The position to add the SQL syntax
     */
    public function raw(string $query, string $index = self::SECTION_WHERE): self
    {
        $this->SQL[$index][] = $query;
        return $this;
    }

    /**
     * Set additional values for insert and update query
     *
     * This method allows you to specify additional columns and their corresponding values when performing
     * an insert or update operation in a database query.
     *
     * @param string $column The column to insert or update
     * @param string $value The value to be inserted or used for updating the specified column.
     * @param int $valueTerm Whether to treat the value as a column/variable or datatype:(string, number...)
     *
     * @return self
     */
    public function set(string $column, ?string $value, int $valueTerm = self::FILTER_QUOTE): self
    {
        // Only INSERT && UPDATE can use this method;
        if($this->isType(self::TYPE_INSERT) || $this->isType(self::TYPE_UPDATE)) {
            $column = $this->backtick($column);
            $this->SQL[self::SECTION_VALUES][$column] = $this->isolate($value, $valueTerm);
        };
        return $this;
    }


}
