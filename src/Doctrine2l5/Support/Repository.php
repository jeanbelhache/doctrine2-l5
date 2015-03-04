<?php

namespace Doctrine2l5\Support;

use Config;

/**
 * jeanbelhache/doctrine2-l5 - Brings Doctrine2 to Laravel 5.
 *
 * @author Jean Belhache <jeanbelhache@gmail.com>
 * @copyright Copyright (c) 2015 Belle EURL
 * @license MIT
 */
class Repository {

    /**
     * The entity manager
     */
    private $d2em = null;

    public function __construct( \Doctrine\ORM\EntityManagerInterface $d2em )
    {
        $this->d2em = $d2em;
    }

    public function r( $repository, $namespace = null )
    {
        if( $namespace == null ) {
            if( strpos( $repository, '\\' ) === false )
                $repository = Config::get( 'd2doctrine.namespaces.models' ) . '\\' . $repository;
        } else {
            $repository = $namespace . '\\' . $repository;
        }

        return $this->d2em->getRepository( $repository );
    }
}
