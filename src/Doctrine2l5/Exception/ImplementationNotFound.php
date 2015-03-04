<?php

namespace Doctrine2l5\Exception;

/**
 * jeanbelhache/doctrine2-l5 - Brings Doctrine2 to Laravel 5.
 *
 * @author Jean Belhache <jeanbelhache@gmail.com>
 * @copyright Copyright (c) 2015 Belle EURL
 * @license MIT
 */
class ImplementationNotFound extends \Exception {

    public function __construct( $message = null, $code = 0, Exception $previous = null )
    {
        return parent::__construct(
            "No class / implementation found for {$message}", $code, $previous
        );
    }

}
