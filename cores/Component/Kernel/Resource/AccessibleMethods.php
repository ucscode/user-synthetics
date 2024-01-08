<?php

namespace Uss\Component\Kernel\Resource;

class AccessibleMethods
{

    public function __construct(protected object $entity, protected array $accessibleMethods)
    {
    }

    public function __call(string $originalMethod, mixed $args): mixed
    {
        $convention = $this->composeMethodConvention($originalMethod);
        
        foreach($convention as $method) {

            $isAccessible = in_array(
                strtolower($method), 
                array_map('strtolower', $this->accessibleMethods)
            );

            if ($isAccessible && method_exists($this->entity, $method)) {
                return call_user_func_array([$this->entity, $method], $args);
            }
        }

        if(method_exists($this->entity, $originalMethod)) {
            throw new \RuntimeException(
                "Call to method `{$originalMethod}` is not allowed within twig templates."
            );
        }
    }

    protected function composeMethodConvention(string $method): array
    {
        $convention = [null, 'get', 'is', 'has'];
        array_walk($convention, function(&$value) use ($method) {
            $value = $value === null ? $method : $value . ucfirst($method);
        });
        return $convention;
    }
}
