<?php

namespace Appzcoder\Routing;

use Appzcoder\AliasMaker\Facade;

class RouterFacade extends Facade
{

    protected static function getFacadeAccessor()
    {
        return 'Appzcoder\\Routing\\Router';
    }

}
