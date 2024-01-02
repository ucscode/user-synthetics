<?php

namespace Uss\Component\Kernel\Abstract;

use Ucscode\Pairs\Pairs;
use Ucscode\SQuery\SQuery;
use Uss\Component\Database;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\Extension\DebugExtension;
use Ucscode\SQuery\Condition;
use Uss\Component\Kernel\System\Prime as KernelPrime;
use DateTime;
use DateTimeInterface;
use mysqli_result;
use Uss\Component\Kernel\Interface\UssInterface;
use Uss\Component\Kernel\Uss;
use Uss\Component\Kernel\UssImmutable;

abstract class AbstractUss implements UssInterface
{
    public readonly FilesystemLoader $filesystemLoader;
    public readonly Environment $twigEnvironment;
    public readonly ?Pairs $options;
    public readonly ?\mysqli $mysqli;
    public array $jsCollection = [];
    public array $twigContext;

    protected function __construct(bool $kernel = false)
    {
        $this->twigContext = [
            'html_language' => 'en',
            'page_title' => UssImmutable::PROJECT_NAME,
            'page_icon' => $this->pathToUrl(UssImmutable::ASSETS_DIR . '/images/origin.png'),
            'page_description' => "A Modular PHP Framework for Building Customized Web Applications",
        ];

        $this->filesystemLoader = new FilesystemLoader([UssImmutable::TEMPLATES_DIR]);
        $this->filesystemLoader->addPath(UssImmutable::TEMPLATES_DIR, self::NAMESPACE);
        $this->twigEnvironment = new Environment($this->filesystemLoader, [
            'debug' => UssImmutable::DEBUG,
        ]);
        $this->twigEnvironment->addExtension(new DebugExtension());

        $kernelPrime = new KernelPrime($this);

        if($kernel) {
            $this->mysqli = $kernelPrime->getMysqliInstance();
            $this->options = $kernelPrime->getPairsInstance($this->mysqli);
            $kernelPrime->createSession(self::SESSION_KEY, self::CLIENT_KEY);
            $kernelPrime->loadHTMLResource();
        }
    }

    /**
     * Fetch a single row of item from the database using specified conditions
     */
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
            $data = !$sqlEscape ?
            htmlentities($data) :
            ($this->mysqli ? $this->mysqli->real_escape_string($data) : addslashes($data));
        };
        return $data;
    }

    /**
     * Generate URL from an absolute filesystem path.
     *
     * @param string $pathname The pathname to be converted in the URL.
     * @param bool $hidebase Whether to hide the URL base or not.
     */
    public function pathToUrl(string $pathname, bool $hideProtocol = false): string
    {
        $pathname = $this->slash($pathname); // Necessary in windows OS
        $port = $_SERVER['SERVER_PORT'];
        $scheme = ($_SERVER['REQUEST_SCHEME'] ?? ($port != 80 ? 'https' : 'http'));
        $viewPort = !in_array($port, ['80', '443']) ? ":{$port}" : null;
        $requestUri = preg_replace("~^{$_SERVER['DOCUMENT_ROOT']}~i", '', $pathname);

        return (!$hideProtocol || $viewPort) ?
            $scheme . "://" . $_SERVER['SERVER_NAME'] . "{$viewPort}" . $requestUri :
            $requestUri;
    }

    /**
     * Convert array to HTML Attributes
     *
     * @param array $array The array containing the key-value pairs to be converted.
     * @param bool $singleQuote Whether to use single quotes for attribute values. Default is `false`.
     * @return string The HTML attribute string
     */
    public function arrayToHtmlAttrs(array $array, bool $singleQuote = false): string
    {
        return implode(" ", array_map(function ($key, $value) use ($singleQuote) {
            if(is_array($value)) {
                $value = json_encode($value);
            }
            $value = htmlspecialchars($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $quote = $singleQuote ? "'" : '"';
            return "{$key}={$quote}{$value}{$quote}";
        }, array_keys($array), array_values($array)));
    }


    /**
     * Generate a Random Key
     *
     * @param int $length The length of the key to be generated. Default is 10.
     * @param bool $use_spec_char Whether to include special characters in the key. Default is `false`.
     * @return string The generated random key.
     */
    public function keygen(int $length = 10, bool $use_special_char = false): string
    {
        $data = [...range(0, 9), ...range('a', 'z'), ...range('A', 'Z')];
        if($use_special_char) {
            $special = ['!', '@', '#', '$', '%', '^', '&', '*', '(', ')', '[', ']', '{', '}', '/', ':', '.', ';', '|', '>', '~', '_', '-'];
            $data = [...$data, ...$special];
        };
        $key = '';
        for($x = 0; $x < $length; $x++) {
            shuffle($data);
            $key .= $data[0];
        };
        return $key;
    }

    /**
     * Calculate Elapsed Time (E.g 3 days ago)
     *
     * @param DateTimeInterface|int|string $DateTime - A DateTimeInterface object, timestamp string, or Unix timestamp
     * @param bool $verbose - Determines the level of detail in the time output.
     * @return string The elapsed time in a human-readable format.
     */
    public function relativeTime(DateTimeInterface|int|string $time, bool $verbose = false): string
    {
        if(!($time instanceof DateTimeInterface)) {
            $time = !is_numeric($time) ? new DateTime($time) : (new DateTime())->setTimestamp($time);
        }

        $interval = (new DateTime())->diff($time);

        $formats = [
            'y' => 'year',
            'm' => 'month',
            'd' => 'day',
            'h' => 'hour',
            'i' => 'minute',
            's' => 'second'
        ];

        $result = 'Just Now';

        foreach($formats as $key => $unit) {
            $value = $interval->{$key};
            if($value > 0) {
                $unit = !$verbose ? substr($unit, 0, 1) : ' ' . $unit . ($value > 1 ? 's' : '');
                $result = $interval->format("%{$key}{$unit} ago");
                break;
            }
        };

        return $result;
    }

    /**
     * Get the availabe columns of a table
     *
     * This method scans a table in the database schema and return all available columns associated with the table
     *
     * @param string $tableName: The name of the table to retrive the columns
     * @return array: A list of all the columns
     */
    public function getTableColumns(string $tableName): array
    {
        $columns = [];

        $squery = (new SQuery())
            ->select('COLUMN_NAME')
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
                $value = $column['column_name'] ?? $column['COLUMN_NAME'];
                $columns[$value] = $value;
            }
        };

        return $columns;
    }

    /**
     * Converts a mysqli_result object to an associative array.
     *
     * @param mysqli_result $result The mysqli_result object to convert.
     * @param callable|null $mapper Optional. A callback function to apply to each row before adding it to the result. The callback should accept a value and a key as its arguments.
     * @return array The resulting associative array.
     */
    public function mysqliResultToArray(mysqli_result $result, ?callable $mapper = null): array
    {
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $mapper ? array_combine(array_keys($row), array_map($mapper, $row, array_keys($row))) : $row;
        }
        return $data;
    }

    /**
     * Check if the given path is an absolute path.
     *
     * @param string $path The path to check.
     * @return bool
     */
    public function isAbsolutePath(string $path): bool
    {
        return  preg_match('#^[a-z][a-z\d+.-]*://#i', $path) ||
                preg_match('#^[a-z]:(?:\\\\|/)#i', $path) ||
                preg_match('#^/|~/#', $path);
    }

    /**
     * Implode but use 'and' to join the last entity
     */
    public function implodeReadable(?array $array, ?string $binder = 'and'): string
    {
        if (count($array) > 1) {
            $last = array_pop($array);
            return implode(", ", $array) . " {$binder} " . $last;
        }
        return array_pop($array);
    }

    /**
     * Replaces backslashes with forward slashes in a given string.
     */
    protected function slash(?string $path): string
    {
        return str_replace("\\", "/", $path);
    }
}
