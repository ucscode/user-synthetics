<?php

namespace Uss\Component\Kernel;

use Ucscode\Pairs\Pairs;
use Uss\Component\Kernel\Abstract\AbstractUss;
use Uss\Component\Trait\SingletonTrait;
use Uss\Component\Kernel\System\Prime as KernelPrime;
use Uss\Component\Kernel\Interface\UssInterface;
use Ucscode\SQuery\SQuery;
use Ucscode\SQuery\Condition;
use Uss\Component\Kernel\Extension\Extension;

final class Uss extends AbstractUss implements UssInterface
{
    use SingletonTrait;

    protected Extension $extension;
    public readonly ?Pairs $options;
    public readonly ?\mysqli $mysqli;

    protected function __construct(bool $kernel = false)
    {
        parent::__construct();
        
        $this->extension = new Extension($this);
        $this->twigEnvironment->addExtension($this->extension);

        if($kernel) {
            $kernelPrime = new KernelPrime($this);
            $kernelPrime->loadHTMLResource();
            $this->mysqli = $kernelPrime->getMysqliInstance();
            $this->options = $kernelPrime->getPairsInstance($this->mysqli);
            $kernelPrime->createSession(UssImmutable::APP_SESSION_KEY, UssImmutable::APP_CLIENT_KEY);
        }
    }

    public function render(string $templateFile, array $variables = [], bool $return = false): ?string
    {
        $this->extension->configureRenderContext();
        $variables += $this->twigContext;
        $result = $this->twigEnvironment->render($templateFile, $variables);
        return $return ? $result : call_user_func(function () use ($result) {
            print($result);
            die();
        });
    }

    public function terminate(bool|int|null $status, ?string $message = null, mixed $data = []): void
    {
        $response = [
            "status" => $status, 
            "message" => $message, 
            "data" => $data
        ];
        exit(json_encode($response, JSON_PRETTY_PRINT));
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
            ->select(['COLUMN_NAME', 'DATA_TYPE', 'IS_NULLABLE'])
            ->from('information_schema.COLUMNS')
            ->where(
                (new Condition())
                    ->add('TABLE_SCHEMA', $_ENV['DB_NAME'])
                    ->and('TABLE_NAME', $tableName)
            )
            ->orderBy('ORDINAL_POSITION', null);

        $SQL = $squery->build();
        $result = Uss::instance()->mysqli->query($SQL);

        if($result->num_rows) {
            while($column = $result->fetch_assoc()) {
                $key = $column['column_name'] ?? $column['COLUMN_NAME'];
                $value = $column['data_type'] ?? $column['DATA_TYPE'];
                $nullable = $column['is_nullable'] ?? $column['IS_NULLABLE'];
                $columns[$key] = [
                    'datatype' => strtoupper($value),
                    'nullable' => $nullable != 'NO'
                ];
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

    public function array_map_recursive(callable $callback, array $array): array
    {
        return array_map(
            fn ($item) => is_array($item) ? 
                $this->array_map_recursive($callback, $item) : 
                call_user_func($callback, $item),
            $array
        );
    }

    public function replaceVars(string $string, array $data): string
    {
        $chars = 'a-z0-9_\-\.\$\(\)\[\]:;@#';
        $new_string = preg_replace_callback("~%(?:\\\\)*\{([$chars]+)\}~i", function ($match) use ($data) {
            if(substr($match[0], 0, 2) != '%{') {
                return ('%' . substr($match[0], 2));
            }
            $key = $match[1];
            return $data[$key] ?? null;
        }, $string);
        return $new_string;
    }
};
