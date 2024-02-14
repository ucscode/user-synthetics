<?php

namespace Uss\Component\Kernel\Resource;

class AccessibleMethods
{

    public function __construct(protected array $accessibleMethods, protected object $classInstance)
    {}

    public function __call(string $originalMethod, mixed $args): mixed
    {
        $convention = $this->generateMethodConventions($originalMethod);
        
        foreach($convention as $method) {

            $isAccessible = in_array(
                strtolower($method), 
                array_map('strtolower', $this->accessibleMethods)
            );

            if ($isAccessible && method_exists($this->classInstance, $method)) {
                return call_user_func_array([$this->classInstance, $method], $args);
            }
        }

        if(method_exists($this->classInstance, $originalMethod)) {
            throw new \RuntimeException(
                sprintf("Call to extension method `%s()` is not allowed within twig templates.", $originalMethod)
            );
        }

        throw new \RuntimeException(
            sprintf("Call to undefined extension method `%s()`.", $originalMethod)
        );
    }

    protected function generateMethodConventions(string $method): array
    {
        $convention = [null, 'get', 'is', 'has'];
        array_walk($convention, function(&$value) use ($method) {
            $value = $value === null ? $method : $value . ucfirst($method);
        });
        return $convention;
    }

    public function __debugInfo()
    {
        return $this->accessibleMethods;
    }
}
