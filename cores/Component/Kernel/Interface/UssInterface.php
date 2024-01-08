<?php

namespace Uss\Component\Kernel\Interface;

use mysqli_result;

interface UssInterface
{
    public const NAMESPACE = 'Uss';
    public const SESSION_KEY = 'UssId'; // (PHP Session)
    public const CLIENT_KEY = 'uss_client_id'; // (Browser Cookie)

    public function mysqliResultToArray(mysqli_result $result, ?callable $mapper = null): array;
    public function getTableColumns(string $tableName): array;
    public function implodeReadable(?array $array, ?string $binder = 'and'): string;
}
