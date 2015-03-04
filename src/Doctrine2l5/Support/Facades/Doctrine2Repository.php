<?php

namespace Doctrine2l5\Support\Facades;

use Illuminate\Support\Facades\Facade;


/**
 * jeanbelhache/doctrine2-l5 - Brings Doctrine2 to Laravel 5.
 *
 * @author Jean Belhache <jeanbelhache@gmail.com>
 * @copyright Copyright (c) 2015 Belle EURL
 * @license MIT
 */
class Doctrine2Repository extends Facade {

        /**
         * Get the registered name of the component.
         *
         * @return string
         */
        protected static function getFacadeAccessor() { return '\Doctrine2Bridge\Support\Repository'; }

}
