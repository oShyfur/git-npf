<?php

namespace Np\Structure\Facades;

use October\Rain\Support\Facade;

class Oisf extends Facade
{
    /**
     * Get the registered name of the component.
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'oisf.api';
    }
}
