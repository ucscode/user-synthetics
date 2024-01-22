<?php

namespace Uss\Component\Kernel;

use mysqli_result;
use Ucscode\Pairs\Pairs;
use Uss\Component\Kernel\Abstract\AbstractUss;
use Uss\Component\Trait\SingletonTrait;
use Uss\Component\Kernel\System\Prime as KernelPrime;
use Uss\Component\Kernel\Interface\UssInterface;
use Ucscode\SQuery\SQuery;
use Uss\Component\Database;
use Ucscode\SQuery\Condition;

final class Uss extends AbstractUss implements UssInterface
{
    use SingletonTrait;

    public readonly ?Pairs $options;
    public readonly ?\mysqli $mysqli;

    protected function __construct(bool $kernel = false)
    {
        parent::__construct();

        if($kernel) {
            $kernelPrime = new KernelPrime($this);
            $kernelPrime->loadHTMLResource();
            $this->mysqli = $kernelPrime->getMysqliInstance();
            $this->options = $kernelPrime->getPairsInstance($this->mysqli);
            $kernelPrime->createSession(UssImmutable::SESSION_KEY, UssImmutable::CLIENT_KEY);
        }
    }

    public function mysqliResultToArray(mysqli_result $result, ?callable $mapper = null): array
    {
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $mapper ? array_combine(array_keys($row), array_map($mapper, $row, array_keys($row))) : $row;
        }
        return $data;
    }

    public function fetchItem(string $table, string|array $value, $column = 'id'): ?array
    {
        $state = is_array($value) ? $value : [$column => $value];
        $condition = new Condition();

        foreach($state as $key => $input) {
            $condition->add($key, $input);
        }

        $squery = (new SQuery())
            ->select()
            ->from($table)
            ->where($condition);

        $SQL = $squery->build();
        $result = $this->mysqli->query($SQL);

        return $result->fetch_assoc();
    }

    /**
     * Escape SQL injection Queries or decode HTML Entities in a single data or an iteratable
     */
    public function sanitize(mixed $data, bool $sqlEscape = false): mixed
    {
        if(is_iterable($data)) {
            foreach($data as $key => $value) {
                $key = htmlentities($key);
                $value = $this->sanitize($value, $sqlEscape);
                is_object($data) ? $data->{$key} = $value : $data[$key] = $value;
            }
        } else {
            $data = !$sqlEscape ? htmlentities($data) :
                (
                    $this->mysqli ?
                        $this->mysqli->real_escape_string($data) :
                        addslashes($data)
                );
        };
        return $data;
    }

    public function getTableColumns(string $tableName): array
    {
        $columns = [];

        $squery = (new SQuery())
            ->select(['COLUMN_NAME', 'DATA_TYPE'])
            ->from('information_schema.COLUMNS')
            ->where(
                (new Condition())
                    ->add('TABLE_SCHEMA', Database::NAME)
                    ->and('TABLE_NAME', $tableName)
            )
            ->orderBy('ORDINAL_POSITION', null);

        $SQL = $squery->build();
        $result = Uss::instance()->mysqli->query($SQL);

        if($result->num_rows) {
            while($column = $result->fetch_assoc()) {
                $key = $column['column_name'] ?? $column['COLUMN_NAME'];
                $value = $column['data_type'] ?? $column['DATA_TYPE'];
                $columns[$key] = strtoupper($value);
            }
        };
        
        return $columns;
    }

    public function implodeReadable(?array $array, ?string $binder = 'and'): string
    {
        if (count($array) > 1) {
            $last = array_pop($array);
            return implode(", ", $array) . " {$binder} " . $last;
        }
        return array_pop($array) ?? '';
    }
};
