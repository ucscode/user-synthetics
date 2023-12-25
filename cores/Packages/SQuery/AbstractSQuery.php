<?php

namespace Ucscode\SQuery;

use Exception;
use mysqli;

abstract class AbstractSQuery implements SQueryInterface
{
    use SQueryTrait;

    protected ?string $DMLS = null; // Data Manipulation Language Statement
    protected array $table = [];
    protected array $columns = [];
    protected array $values = [];
    protected Condition $where;
    protected array $group_by = [];
    protected Condition $having;
    protected array $order_by = [];
    protected ?int $limit = null;
    protected ?int $offset = null;

    abstract public function from(string $table);

    public function __construct(protected ?mysqli $mysqli = null)
    {
        $this->where = new Condition($mysqli);
        $this->having = new Condition($mysqli);
    }

    public function build(): string
    {
        switch($this->DMLS) {
            case self::KEYWORD_SELECT:
                $syntax = [
                    $this->DMLS . ' ' . implode(", ", $this->columns),
                    self::KEYWORD_FROM . ' ' . implode(' AS ', $this->table),
                    $this->where->build(self::KEYWORD_WHERE),
                    !empty($this->group_by) ? self::KEYWORD_GROUP_BY . ' ' . implode(", ", $this->group_by) : null,
                    $this->having->build(self::KEYWORD_HAVING),
                    !empty($this->order_by) ? self::KEYWORD_ORDER_BY . ' ' . implode(", ", $this->order_by) : null,
                    !is_null($this->limit) ? self::KEYWORD_LIMIT . ' ' . $this->limit : null,
                    !is_null($this->offset) && !is_null($this->limit) ? self::KEYWORD_OFFSET . ' ' . $this->offset : null,
                ];
                break;

            case self::KEYWORD_INSERT:
                $syntax = [
                    $this->DMLS . ' INTO ' . $this->table[0],
                    '(' . implode(", ", $this->columns) . ')',
                    'VALUES',
                    '(' . implode(", ", $this->values) . ')',
                ];
                break;

            case self::KEYWORD_UPDATE:
                $syntax = [
                    $this->DMLS . ' ' . $this->table[0] . ' SET',
                    call_user_func(function() {
                        $formation = [];
                        foreach(array_combine($this->columns, $this->values) as $key => $value) {
                            $formation[] = "{$key} = {$value}";
                        }
                        return implode(",\n", $formation);
                    }),
                    $this->where->build(self::KEYWORD_WHERE)
                ];
                break;

            case self::KEYWORD_DELETE:
                $syntax = [
                    $this->DMLS,
                    self::KEYWORD_FROM . ' ' . $this->table[0],
                    $this->where->build(self::KEYWORD_WHERE)
                ];
                break;

            default:
                $syntax = [];
        }

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
        $this->columns = array_map(fn ($value) => $this->tick($value), array_keys($data));
        $this->values = array_map(fn ($value) => $this->surround($value, "'"), array_values($data));
        return $this;
    }
}
