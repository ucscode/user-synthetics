<?php

namespace Uss\Component\Kernel\Extension;

use Uss\Component\Kernel\Resource\AccessibleMethods;
use Uss\Component\Kernel\Resource\AccessibleProperties;

interface ExtensionInterface
{
    public function props(): AccessibleProperties;
    public function meths(): AccessibleMethods;
}