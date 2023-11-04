<?php

$autoloader = call_user_func(function () 
{
    $iterator = function (string $dir) {
        return new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(
                UssImmutable::SRC_DIR . '/' . $dir,
                FilesystemIterator::SKIP_DOTS
            )
        );
    };
    return [$iterator('bundles')];
});

spl_autoload_register(function ($className) use ($autoloader) 
{
    $split = explode('\\', $className);

    if(count($split) > 1 && $split[0] === 'Ucscode') {
        $filepath = UssImmutable::SRC_DIR . "/packages/{$split[1]}/{$split[2]}.php";
        if(is_file($filepath)) {
            return require $filepath;
        }
    };
    
    foreach($autoloader as $iterator) {
        foreach($iterator as $fileinfo) {
            if(strtoupper($fileinfo->getExtension()) === 'PHP') {
                $filename = $fileinfo->getBasename('.php');
                if($className === $filename) {
                    require $fileinfo->getPathname();
                    break;
                }
            };
        }
    }
});

