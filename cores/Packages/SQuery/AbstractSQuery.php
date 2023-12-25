<?php

namespace Ucscode\SQuery;

use Exception;

abstract class AbstractSQuery implements SQueryInterface
{
    use SQueryTrait;
    
    protected ?string $DMLS = null; // Data Manipulation Language Statement
    protected ?string $table = null;
    protected array $columns = [];
    protected array $values = [];
    protected Condition $where;
    protected array $group_by = [];
    protected Condition $having;
    protected array $order_by = [];
    protected ?int $limit = null;
    protected ?int $offset = null;

    abstract public function from(string $table);

    public function __construct()
    {
        $this->where = new Condition();
        $this->having = new Condition();
    }

    public function build(): string
    {
        switch($this->DMLS) {
            case self::KEYWORD_SELECT:
                $syntax = [
                    $this->DMLS,
                    implode(", ", $this->columns),
                    self::KEYWORD_FROM . ' ' . $this->table,
                    $this->where->build(self::KEYWORD_WHERE),
                    !empty($this->group_by) ? self::KEYWORD_GROUP_BY . ' ' . implode(", ", $this->group_by) : null,
                    $this->having->build(self::KEYWORD_HAVING),
                    !empty($this->order_by) ? self::KEYWORD_ORDER_BY . ' ' . implode(", ", $this->order_by) : null,
                    !is_null($this->limit) ? self::KEYWORD_LIMIT . ' ' . $this->limit : null,
                    !is_null($this->offset) && !is_null($this->limit) ? self::KEYWORD_OFFSET . ' ' . $this->offset : null
                ];
                break;
        }
        var_dump($this);
        return implode("\n", array_filter($syntax));
    }

    protected function setDMLS(string $dmls)
    {
        if($this->DMLS) {
            throw new \Exception(
                sprintf(
                    'Cannot modify the "%s" Data Manipulation Language Statement (DMLS) in the current instance of "%s". Please create a new instance for any additional operations.',
                    $this->DMLS,
                    get_called_class()
                )
            );
        };
        $this->DMLS = $dmls;
    }

    protected function upsert(string $table, array $data): self
    {
        $this->from($table);
        $this->columns = array_keys($data);
        $this->values = array_values($data);
        return $this;
    }
}
