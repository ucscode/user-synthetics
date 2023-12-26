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
    public function select(string|array $columns = '*'): self
    {
        $this->setDMLS(self::KEYWORD_SELECT);
        if(is_string($columns)) {
            $columns = [$columns];
        };
        $this->columns = array_map(function ($value) {
            $value = array_map(function ($entity) {
                return $this->tick($entity);
            }, preg_split("/as/i", $value));
            return implode(" AS ", $value);
        }, array_values($columns));
        return $this;
    }

    public function delete(): self
    {
        $this->setDMLS(self::KEYWORD_DELETE);
        return $this;
    }

    public function from(string $table, ?string $alias = null): self
    {
        $this->table = array_map([$this, 'tick'], array_filter([$table, $alias]));
        return $this;
    }

    public function insert(string $table, array $data): self
    {
        $this->setDMLS(self::KEYWORD_INSERT);
        return $this->upsert($table, $data);
    }

    public function update(string $table, array $data): self
    {
        $this->setDMLS(self::KEYWORD_UPDATE);
        return $this->upsert($table, $data);
    }

    public function where(Condition $condition): self
    {
        $this->where = $condition;
        return $this;
    }

    public function getWhereCondition(): Condition
    {
        return $this->where;
    }

    public function groupBy(string|array $group): self
    {
        if(!is_array($group)) {
            $group = [$group];
        }
        $group = array_map(function ($value) {
            return $this->tick($value);
        }, array_values($group));
        $this->group_by = array_merge($this->group_by, $group);
        return $this;
    }

    public function having(Condition $condition): self
    {
        $this->having = $condition;
        return $this;
    }

    public function getHavingCondition(): Condition
    {
        return $this->having;
    }

    public function orderBy(string|array $order, string $direction = 'ASC'): self
    {
        if(is_array($order)) {
            foreach($order as $key => $direction) {
                if(is_numeric($key)) {
                    $key = $direction;
                    $direction = 'ASC';
                }
                $this->createOrder($key, $direction);
            }
            return $this;
        }
        return $this->createOrder($order, $direction);
    }

    public function limit(?int $limit, ?int $offset = null): self
    {
        $this->limit = is_null($limit) ? $limit : abs($limit);
        if(func_num_args() > 1) {
            $this->offset($offset);
        };
        return $this;
    }

    public function offset(?int $offset): self
    {
        $this->offset = is_null($offset) ? $offset : abs($offset);
        return $this;
    }
}
