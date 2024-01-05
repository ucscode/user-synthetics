<?php

namespace Uss\Component\Kernel\Abstract;

use DateTime;
use DateTimeInterface;
use mysqli_result;
use Uss\Component\Kernel\Interface\UssInterface;
use Uss\Component\Kernel\UssImmutable;

abstract class AbstractUss extends AbstractEnvironmentSystem
{
    /**
     * Retrieve URL request path segments.
     *
     * @param int|null $index - index of the segment to retrieve. If not provided, returns the entire array of segments.
     * @return array|string|null
     */
    public function getUrlSegments(?int $index = null): array|string|null
    {
        $path = explode("?", $_SERVER['REQUEST_URI'])[0] ?? '';
        $path = str_replace($this->useForwardSlash(ROOT_DIR), '', $this->useForwardSlash($_SERVER['DOCUMENT_ROOT']) . $path);
        $request = array_values(array_filter(array_map('trim', explode("/", $path))));
        return !is_null($index) ? ($request[$index] ?? null) : $request;
    }

    /**
     * Generate a one-time security token.
     *
     * @param string $input The secret input used to generate the token. Defaults to '1' if not provided.
     * @param string|null $token The token to verify. If not provided, a new token is generated.
     * @return string|bool If no token is provided, returns a one-time security token. If a token is provided, returns a `boolean` indicating whether the token is valid.
     */
    public function nonce($input = '1', ?string $receivedNonce = null): string|bool
    {
        $secretKey = UssImmutable::SECRET_KEY . md5($_SESSION[UssInterface::SESSION_KEY]);
        $algorithm = 'ripemd160';
        $salt = bin2hex(random_bytes(3));
        $dataToHash = $input . $salt . $secretKey;
        $nonce = hash_hmac($algorithm, $dataToHash, $secretKey);

        if($receivedNonce !== null) {
            $token = explode(':', $receivedNonce);
            if(count($token) === 2) {
                list($expectedNonce, $expectedSalt) = $token;
                $computedNonce = hash_hmac($algorithm, $input . $expectedSalt . $secretKey, $secretKey);
                return hash_equals($computedNonce, $expectedNonce);
            }
            return false;
        }

        return $nonce . ':' . $salt;
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
        return array_pop($array) ?? '';
    }
}
