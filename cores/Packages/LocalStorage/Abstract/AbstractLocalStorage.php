<?php

namespace Ucscode\LocalStorage\Abstract;

use Exception;
use TypeError;
use Ucscode\LocalStorage\Interface\LocalStorageInterface;

abstract class AbstractLocalStorage implements LocalStorageInterface
{
    protected array $storage;

    public function __construct(protected string $filepath)
    {
        $content = file_get_contents($filepath);
        $this->storage = (is_string($content) && !empty($content)) ? ($this->decode($content) ?: []) : [];
        var_dump($this->storage);
    }

    public function &__get(string $offset): mixed
    {
        if(!isset($this->storage[$offset])) {
            $proxy = null;
            return $proxy;
        };
        return $this->storage[$offset];
    }

    public function __set(string $offset, mixed $value): void
    {
        $this->storage[$offset] =& $value;
    }

    public function __isset(string $offset): bool
    {
        return array_key_exists($offset, $this->storage);
    }

    public function __unset(string $offset): void
    {
        if(array_key_exists($offset, $this->storage)) {
            unset($this->storage[$offset]);
        }
    }

    protected function encode(): string
    {
        return 
            gzdeflate(
                bin2hex(
                    str_rot13(
                        base64_encode(
                            serialize(
                                $this->storage
                            )
                        )
                    )
                )
            )
        ;
    }

    protected function decode(string $encoding): bool|array
    {
        $uncompress = @gzinflate($encoding);
        if($uncompress !== false) {
            return
                unserialize(
                    base64_decode(
                        str_rot13 (
                            hex2bin (
                                $uncompress
                            )
                        )
                    )
                )
            ;
        }
        return [];
    }
}