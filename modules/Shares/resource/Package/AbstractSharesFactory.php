<?php

namespace Shares\Package;

use Uss;
use DashboardInterface;

abstract class AbstractSharesFactory
{
    protected DashboardInterface $dashboard;

    protected function path(string $route, bool $href = false): string
    {
        $uss = Uss::instance();
        $result = $uss->filterContext($this->dashboard->config->getBase() . "/" . $route);
        if($href) {
            $result = $uss->abspathToUrl(ROOT_DIR . "/" . $result);
        };
        return $result;
    }
}
