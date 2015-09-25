<?php

namespace Appzcoder\Routing;

use Appzcoder\Facade;

class RouterFacade extends Facade
{

    protected static function getFacadeAccessor()
    {
        return 'Appzcoder\\Routing\\Router';
    }

}
