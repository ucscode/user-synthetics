<?php

namespace Uss\Component\Kernel\Abstract;

use DateTime;
use DateTimeInterface;
use Uss\Component\Kernel\Resource\Enumerator;
use Uss\Component\Kernel\UssImmutable;

abstract class AbstractUss extends AbstractUssEnvironment
{
    public function __construct()
    {
        parent::__construct();
    }

    public function isLocalhost(): bool
    {
        return in_array($_SERVER['SERVER_NAME'], ['localhost', '127.0.0.1', '::1'], true);
    }

    public function getUrlSegments(?int $index = null): array|string|null
    {
        $path = explode("?", $_SERVER['REQUEST_URI'])[0] ?? '';
        $path = str_replace($this->useForwardSlash(ROOT_DIR), '', $this->useForwardSlash($_SERVER['DOCUMENT_ROOT']) . $path);
        $request = array_values(array_filter(array_map('trim', explode("/", $path))));
        return !is_null($index) ? ($request[$index] ?? null) : $request;
    }

    public function nonce($input = '1', ?string $receivedNonce = null): string|bool
    {
        $secretKey = $_ENV['APP_SECRET'] . md5($_SESSION[UssImmutable::APP_SESSION_KEY]);
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

    public function isAbsolutePath(string $path): bool
    {
        return  preg_match('#^[a-z][a-z\d+.-]*://#i', $path) ||
                preg_match('#^[a-z]:(?:\\\\|/)#i', $path) ||
                preg_match('#^/|~/#', $path);
    }

    public function pathToUrl(string $pathname, bool $hideProtocol = false): string
    {
        $pathname = $this->useForwardSlash($pathname); // Necessary in windows OS
        $port = $_SERVER['SERVER_PORT'];
        $scheme = ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? $_SERVER['REQUEST_SCHEME']) ?? ($port != 80 ? 'https' : 'http');
        $visiblePort = !in_array($port, ['80', '443']) ? ":{$port}" : null;
        $requestURI = preg_replace("~^{$_SERVER['DOCUMENT_ROOT']}~i", '', $pathname);
        $fullRequestURI = $scheme . "://" . $_SERVER['SERVER_NAME'] . "{$visiblePort}" . $requestURI;
        return (!$hideProtocol || $visiblePort) ? $fullRequestURI : $requestURI;
    }

    public function filterContext(null|string|array $path, string $divider = '/'): string
    {
        if(is_array($path)) {
            $path = implode($divider, $path);
        };
        $explosion = array_filter(array_map('trim', explode("/", $path)));
        return implode("/", $explosion);
    }

    public function getTemplateSchema(?string $templatePath = UssImmutable::APP_NAMESPACE, Enumerator $enum = Enumerator::FILE_SYSTEM, int $index = 0): string
    {
        $templatePath = $this->filterContext($templatePath);
        if(!preg_match('/^@\w+/i', $templatePath)) {
            $templatePath = '@' . UssImmutable::APP_NAMESPACE . '/' . $templatePath;
        }

        $context = explode("/", $templatePath);
        $namespace = str_replace('@', '', array_shift($context));
        $filesystem = $this->filesystemLoader->getPaths($namespace)[$index] ?? null;
        $prefix = '';

        if($filesystem) {
            $prefix = match($enum) {
                Enumerator::FILE_SYSTEM => $filesystem,
                Enumerator::THEME => "@{$namespace}",
                default => $this->pathToUrl($filesystem)
            };
        }

        return $prefix . '/' . $this->filterContext(implode('/', $context));
    }

    public function getServerInfo(): array
    {
        $serverInfo = [
            'server_name' => $_SERVER['SERVER_NAME'],
            'port' => (int)$_SERVER['SERVER_PORT'],
            'host_name' => $_SERVER['SERVER_NAME'],
            'request' => $_SERVER['REQUEST_URI'],
        ];

        $serverInfo['host_name'] .= !in_array($serverInfo['port'], [80, 443], true) ? ":{$_SERVER['SERVER_PORT']}" : null;
        $serverInfo['scheme'] = $_SERVER['REQUEST_SCHEME'] ?? ($serverInfo['port'] === 80 ? 'http' : 'https');
        $serverInfo += parse_url($serverInfo['request']);
        $serverInfo['query'] ??= '';
        parse_str($serverInfo['query'], $serverInfo['query_params']);
        return $serverInfo;
    }

    public function replaceUrlQuery(?array $data = null, ?string $urlPath = null): string
    {
        if($urlPath === null) {
            $serverInfo = $this->getServerInfo();
            $urlPath = $serverInfo['scheme'] . '://' . $serverInfo['host_name'] . $serverInfo['path'];
        }
        !empty($data) ? $urlPath .= "?" . http_build_query($data) : null;
        return $urlPath;
    }

    /**
     * Replaces backslashes with forward slashes in a given string.
     */
    protected function useForwardSlash(?string $path): string
    {
        return str_replace("\\", "/", $path);
    }
}
