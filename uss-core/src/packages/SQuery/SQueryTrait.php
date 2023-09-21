<?php

namespace Ucscode\SQuery;

trait SQueryTrait {

    private $__where = 'WHERE';
    private $__and = 'AND';
    private $__or = 'OR';

    private function deriveFilterCondition(
        string|array $column,
        mixed $value = null,
        ?string $operator = null,
        int $keyTerm = self::FILTER_BACKTICK,
        int $valueTerm = self::FILTER_QUOTE
    ): self {

        $__FUNCTION__ = strtoupper(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1)[0]['function']);

        if(gettype($column) === 'string') {
            $column = array($column => $value);
        };
        
        foreach($column as $key => $value) {

            $keyword = null;

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

    private function deriveInfluenceCondition(
        string|array $column,
        mixed $value = null,
        ?string $operator = null,
        int $keyTerm = self::FILTER_BACKTICK,
        int $valueTerm = self::FILTER_QUOTE
    ): self {

        $__FUNCTION__ = strtolower(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1)[0]['function']);

        if(is_string($column)) {
            $column = [$column => $value];
        };

        foreach($column as $key => $value) {

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

        $this->addTableAlias($keyword, $tablename, $as, self::SECTION_JOIN);

        return $this;

    }

    private function addTableAlias(string $keyword, string $tablename, ?string $as, string $index) {
        $query = $keyword . " " . $this->backtick($tablename);
        if($as !== null) {
            $query .= " AS " . $this->backtick($as);
        };
        $this->SQL[$index][] = $query;
    }

}