<?php

namespace Uss\Component\Kernel;

use Symfony\Component\HttpFoundation\Response;
use Twig\TemplateWrapper;
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
    private bool $rendered = false;

    protected function __construct(bool $kernel = false)
    {
        parent::__construct();
        
        $this->extension = new Extension($this);
        $this->twig->addExtension($this->extension);

        if($kernel) {
            $kernelPrime = new KernelPrime($this);
            $kernelPrime->loadHTMLResource();
            $this->mysqli = $kernelPrime->getMysqliInstance();
            $this->options = $kernelPrime->getPairsInstance($this->mysqli);
            $kernelPrime->createSession(UssImmutable::APP_SESSION_KEY, UssImmutable::APP_CLIENT_KEY);
        }
    }

    public function render(string|TemplateWrapper $template, array $variables = [], bool $await = false): Response
    {
        $this->extension->configureRenderContext();
        $variables += $this->templateContext;
        $renderView = $this->twig->render($template, $variables);
        return new Response($renderView);
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
        $fieldset = [
            'COLUMN_NAME', 
            'DATA_TYPE', 
            'IS_NULLABLE',
            'COLUMN_DEFAULT',
            'CHARACTER_MAXIMUM_LENGTH',
            'COLLATION_NAME',
        ];

        $squery = (new SQuery())
            ->select($fieldset)
            ->from('information_schema.COLUMNS')
            ->where(
                (new Condition())
                    ->add('TABLE_SCHEMA', $_ENV['DB_NAME'])
                    ->and('TABLE_NAME', $tableName)
            )
            ->orderBy('ORDINAL_POSITION', null);

        $SQL = $squery->build();
        $result = Uss::instance()->mysqli->query($SQL);

        $columns = [];

        if($result->num_rows) {
            while($column = $result->fetch_assoc()) {
                $key = $column['COLUMN_NAME'];
                foreach($column as $offset => $value) {
                    switch($offset) {
                        case 'DATA_TYPE':
                            $value = strtoupper($value);
                            break;
                        case 'IS_NULLABLE':
                            $value = $value === 'YES';
                            break;
                        case 'COLUMN_DEFAULT':
                            $value = $this->processDefaultTableColumn($value);
                            break;
                    }
                    $column[$offset] = $value;
                }
                $columns[$key] = $column;
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

    private function processDefaultTableColumn(?string $defaultValue): mixed
    {
        if($defaultValue !== null) {
            if ($defaultValue === "NULL") {
                $defaultValue = null;
            } elseif (preg_match("/'(.*)'/", $defaultValue, $matches)) {
                $defaultValue = $matches[1];
            } else {
                $defaultValue = strtoupper($defaultValue);
                if(strpos($defaultValue, 'CURRENT_TIMESTAMP') === 0) {
                    $defaultValue = date('Y-m-d H:i:s');
                }
            }
        }
        return $defaultValue;
    }
};
