<?php

/**
 * jeanbelhache/doctrine2-l5 - Brings Doctrine2 to Laravel 5.
 *
 * @author Jean Belhache <jeanbelhache@gmail.com>
 * @copyright Copyright (c) 2015 Belle EURL
 * @license MIT
 */

namespace Doctrine2l5\Logger;

/**
 * Includes executed SQLs in a Debug Stack.
 *
 * @link   www.doctrine-project.org
 * @since  2.0
 * @author Benjamin Eberlei <kontakt@beberlei.de>
 * @author Guilherme Blanco <guilhermeblanco@hotmail.com>
 * @author Jonathan Wage <jonwage@gmail.com>
 * @author Roman Borschel <roman@code-factory.org>
 */
class Laravel implements \Doctrine\DBAL\Logging\SQLLogger
{
    /**
     * If the logger is enabled (log queries) or not.
     *
     * @var boolean
     */
    public $enabled = true;

    /**
     * For timing queries:
     * @var float|null
     */
    public $start = null;

    /**
     * Query
     */
    public $query = null;

    /**
     * Logging level.
     *
     * Available: debug, info, notice, warning, error, critical, and alert
     *
     * @see http://laravel.com/docs/errors#logging
     */
    public $level = 'debug';

    /**
     * {@inheritdoc}
     */
    public function startQuery($sql, array $params = null, array $types = null)
    {
        if( $this->enabled )
        {
            $this->start = microtime(true);

            $this->query = array( 'sql' => $sql, 'params' => $params, 'types' => $types );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function stopQuery()
    {
        if( $this->enabled )
        {
            $level = $this->level;

            \Log::$level(
                'D2 SQL: '
                    . $this->query['sql']
                    . " [Executed in " . ( microtime(true) - $this->start ) . "secs.] ",
                array( 'params' => $this->query['params'], 'types' => $this->query['types'] )
            );
        }
    }

    /**
     * Set the debugging level
     *
     * @param string $level One of: debug, info, notice, warning, error, critical, and alert
     */
    public function setLevel( $level )
    {
        $this->level = $level;
    }
}
