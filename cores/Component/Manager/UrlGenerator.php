<?php

namespace Uss\Component\Manager;

use Uss\Component\Kernel\Uss;

class UrlGenerator
{
    private string $host; # localhost | domain.com
    private string $base;
    private string $path;
    private array $query = [];
    private array $parameters = [];

    public function __construct(string $path = '/', array $query = [], string $base = '')
    {
        $uss = Uss::instance();
        $this->base = $uss->filterContext($base);
        $this->host = $uss->pathToUrl($_SERVER['DOCUMENT_ROOT']);
        $this->polyfill($path);
        foreach($query as $key => $value) {
            $this->setQuery($key, $value);
        }
    }

    public function __toString()
    {
        return $this->getResult();
    }

    public function setParameter(string $key, ?string $value): self
    {
        $this->parameters[$key] = $value;
        return $this;
    }

    public function setQuery(string $key, ?string $value): self
    {
        $this->query[$key] = $value;
        return $this;
    }

    public function removeQuery(string $key): self
    {
        if(isset($this->query[$key])) {
            unset($this->query[$key]);
        }
        return $this;
    }

    public function getResult(bool $ignoreHost = false)
    {
        $result = $ignoreHost ? '' : $this->host;
        $result .= INSTALLATION_PATH;
        if(!empty($this->base)) {
            $result .= '/' . $this->base;
        }
        if(!empty($this->path)) {
            $path = preg_replace_callback('/\{(\w+)\}/', function ($match) {
                $key = trim($match[1]);
                $value = $this->parameters[$key] ?? null;
                if(is_null($value)) {
                    throw new \Exception(
                        sprintf(
                            'UrlGenerator: Parameter "%s" is not defined for placeholder "{%s}" in the URL path "%s". Make sure to set the value for this parameter using setParameter("%s", ...) before generating the URL.',
                            $key,
                            $key,
                            $this->path,
                            $key
                        )
                    );
                };
                return $value;
            }, $this->path);
            $result .= "/" . trim($path, "/");
        };
        if(!empty($this->query)) {
            $result .= "?" . http_build_query($this->query);
        };
        var_dump($result);
        return $result;
    }

    private function polyfill(string $path): void
    {
        $path = explode("?", $path);
        $uss = Uss::instance();
        $this->path = $uss->filterContext($path[0]);
        if(!empty($path[1])) {
            parse_str($path[1], $this->query);
        };
    }

};
