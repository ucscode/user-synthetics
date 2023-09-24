<?php

namespace Ucscode\SQuery;

trait SQueryTrait
{
    private $__where = 'WHERE';
    private $__and = 'AND';
    private $__or = 'OR';

    /**
     * Derive Conditions Used To Filter Results:
     *
     * The conditions include WHERE, AND, OR.
     * The methods are made available using this trait as
     *
     * ```php
     * use ... {
     *  deriveFilterCondition as public where;
     *  deriveFilterCondition as public and;
     *  deriveFilterCondition as public or;
     * }
     * ```
     *
     * @param array|string $column: A column name (string) or an array containing columns and their associate values
     * @param mixed $value: If parameter 1 is a column name (string), this becomes the column value
     * @param ?string $operator: By default, equal to symbol (`=`) is used for comparism (e.g column = value), this parameter
     * allows you to set custom operator (e.g column LIKE value)
     * @param int $keyTerm: Treat the key as either a (column: variable) or (datatype: string, number)
     * @param int $valueTerm: Treat the value as either a (column: variable) or (datatype: string, number...)
     *
     * @return self: An instance of SQuery
     */
    private function deriveFilterCondition(
        string|array $column,
        mixed $value = null,
        ?string $operator = null,
        int $keyTerm = self::FILTER_BACKTICK,
        int $valueTerm = self::FILTER_QUOTE
    ): self {

        // Get which function is being called: (either: where, and, or)
        $__FUNCTION__ = strtoupper(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1)[0]['function']);

        // Group conditions into an array
        if(!is_array($column)) {
            $column = array($column => $value);
        };

        // For each condition in the array
        foreach($column as $key => $value) {

            $keyword = null;

            // Determine the keyword based on the called function
            switch($__FUNCTION__) {
                case $this->__where:
                    $keyword = $this->hasKeyword($this->__where, self::SECTION_WHERE) ? $this->__and : $this->__where;
                    break;
                case $this->__and:
                    $keyword = !$this->hasKeyword($this->__where, self::SECTION_WHERE) ? $this->__where : $this->__and;
                    break;
                case $this->__or:
                    $keyword = !$this->hasKeyword($this->__where, self::SECTION_WHERE) ? $this->__where : $this->__or;
                    break;
            };

            if($keyword) {
                // Include the condition
                $this->addCondition(
                    $keyword,
                    $key,
                    $value,
                    $operator,
                    $keyTerm,
                    $valueTerm,
                    self::SECTION_WHERE
                );
            }

        };

        return $this;
    }

    /**
     * Generate manipulation condition
     *
     * After "WHERE" clause, there are other clauses used to manipulate data.
     * This includes "ORDER BY", "HAVING" and "GROUP BY".
     * However, since "GROUP BY" clause doesn't use expression, it has it's own dedicated function
     *
     * The methods are made available using this trait as
     *
     * ```php
     * use ... {
     *  deriveInfluenceCondition as public orderBy;
     *  deriveInfluenceCondition as public having;
     * }
     * ```
     *
     * @see SQuery::deriveFilterCondition()    For method parameter documentation.
     */
    private function deriveInfluenceCondition(
        string|array $column,
        mixed $value = null,
        ?string $operator = null,
        int $keyTerm = self::FILTER_BACKTICK,
        int $valueTerm = self::FILTER_QUOTE
    ): self {

        // Get which function is being called: (either: orderBy, and, having)
        $__FUNCTION__ = strtolower(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1)[0]['function']);

        // Group conditions into an array
        if(is_string($column)) {
            $column = [$column => $value];
        };

        // For each condition in the array
        foreach($column as $key => $value) {

            // Determine the associated data based on the called function
            $data = match($__FUNCTION__) {
                'orderby' => [
                    'keyword' => !$this->hasKeyword('ORDER BY', self::SECTION_ORDER_BY) ? 'ORDER BY' : ",",
                    'section' => self::SECTION_ORDER_BY,
                ],
                'having' => [
                    'keyword' => !$this->hasKeyword('HAVING', self::SECTION_HAVING) ? 'HAVING' : 'OR',
                    'section' => self::SECTION_HAVING,
                    'keyTerm' => func_get_args()[3] ?? (!is_null($value) ? self::FILTER_BACKTICK : self::FILTER_QUOTE)
                ]
            };

            $this->addCondition(
                $data['keyword'],
                $key,
                $value,
                $operator,
                $data['keyTerm'] ?? $keyTerm,
                $valueTerm,
                $data['section']
            );

        }

        return $this;

    }

    /**
     * Join one or more table
     *
     * This allows you to join table(s) using "FROM, LEFT JOIN, RIGHT JOIN..."
     *
     * @param string $table: The name of the table to append
     * @param ?string $as: The alias of the appended tableS
     *
     * @return self;
     */
    private function mergeTable(string $tablename, ?string $as = null): self
    {
        $__FUNCTION__ = strtolower(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1)[0]['function']);

        $keyword = match($__FUNCTION__) {
            'from' => $this->hasKeyword('FROM', self::SECTION_FROM) ? 'INNER JOIN' : 'FROM',
            'leftjoin' => 'LEFT JOIN',
            'rightjoin' => 'RIGHT JOIN',
            'innerjoin' => 'INNER JOIN',
            'fulljoin' => 'FULL JOIN',
            'crossjoin' => 'CROSS JOIN'
        };

        $section = ($keyword === self::SECTION_FROM) ? self::SECTION_FROM : self::SECTION_JOIN;

        $this->addTableAlias($keyword, $tablename, $as, $section);

        return $this;

    }

    private function addTableAlias(string $keyword, string $tablename, ?string $as, string $index)
    {
        $query = $keyword . " " . $this->backtick($tablename);
        if($as !== null) {
            $query .= " AS " . $this->backtick($as);
        };
        $this->SQL[$index][] = $query;
    }

}
