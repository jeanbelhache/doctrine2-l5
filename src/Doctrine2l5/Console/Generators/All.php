<?php

namespace Doctrine2l5\Console\Generators;


use Illuminate\Console\Command as LaravelCommand;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;


/**
 * jeanbelhache/doctrine2-l5 - Brings Doctrine2 to Laravel 5.
 *
 * @author Jean Belhache <jeanbelhache@gmail.com>
 * @copyright Copyright (c) 2015 Belle EURL
 * @license MIT
 */
class All extends LaravelCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'd2:generate:all';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate all Doctrine2 entities, proxies and repositoies';

    public function fire()
    {
        $this->call( 'd2:generate:entities' );
        $this->call( 'd2:generate:proxies' );
        $this->call( 'd2:generate:repositories' );
    }

}
