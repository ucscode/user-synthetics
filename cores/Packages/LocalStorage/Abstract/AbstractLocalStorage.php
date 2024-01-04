<?php

namespace Ucscode\LocalStorage\Abstract;

use Exception;
use TypeError;
use Ucscode\LocalStorage\Interface\LocalStorageInterface;

abstract class AbstractLocalStorage implements LocalStorageInterface
{
    protected const ALGORITHM = 'aes-256-cbc';
    protected string $iv;
    protected array $storage;

    public function __construct(protected string $filepath, protected ?string $secretKey = null)
    {
        $this->iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length(self::ALGORITHM));
        $content = file_get_contents($filepath);
        $this->storage = (!empty($content)) ? ($this->decode($content) ?: []) : [];
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
        $encoding = 
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
            );

        if(!empty($this->secretKey)) 
        {
            $encryption = openssl_encrypt(
                $encoding,
                self::ALGORITHM,
                $this->secretKey,
                OPENSSL_RAW_DATA,
                $this->iv
            );

            $encoding = $this->iv . $encryption;
        }

        return $encoding;
    }

    protected function decode(string $encoding): bool|array
    {
        if(!empty($this->secretKey)) 
        {
            $IV = substr($encoding, 0, openssl_cipher_iv_length(self::ALGORITHM));
            $encryption = substr($encoding, openssl_cipher_iv_length(self::ALGORITHM));

            $encoding = openssl_decrypt(
                $encryption,
                self::ALGORITHM,
                $this->secretKey,
                OPENSSL_RAW_DATA,
                $IV
            );
            
            if($encoding === false) {
                throw new \Exception(
                    "Cannot access LocalStorage components with incorrect security key"
                );
            }
        }

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