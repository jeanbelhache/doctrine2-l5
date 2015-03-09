<?php

namespace Doctrine2l5\Console\Generators;


use Illuminate\Console\Command as LaravelCommand;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

use Doctrine\ORM\EntityManagerInterface;

use Config;

/**
 * jeanbelhache/doctrine2-l5 - Brings Doctrine2 to Laravel 5.
 *
 * @author Jean Belhache <jeanbelhache@gmail.com>
 * @copyright Copyright (c) 2015 Belle EURL
 * @license MIT
 */
class Proxies extends LaravelCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'd2:generate:proxies';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Doctrine2 proxies for entities.';

    /**
     * The Entity Manager
     *
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    private $d2em;

    public function __construct(\Doctrine\ORM\EntityManagerInterface $d2em)
    {
        parent::__construct();

        $this->d2em = $d2em;
    }

    public function fire()
    {
        $this->info('Starting proxy generation....');

        // flush all generated and cached entities, etc
        \D2Cache::flushAll();

        try {
            $metadata = $this->d2em->getMetadataFactory()->getAllMetadata();
        } catch( \Doctrine\Common\Persistence\Mapping\MappingException $e ) {
            if( $this->option( 'verbose' ) == 3 )
                throw $e;

            $this->error( "Caught Doctrine\Common\Persistence\Mapping\MappingException: " . $e->getMessage() );
            $this->info( "Re-optimizing:" );
            $this->call( 'optimize' );
            $this->comment( "*** You must now rerun this artisan command ***" );
            exit(-1);
        }

        if( empty($metadata) ) {
            $this->error('No metadata found to generate entities.');
            return -1;
        }

        $directory = Config::get( 'd2doctrine.paths.proxies' );

        if( !$directory ) {
            $this->error('The proxy directory has not been set.');
            return -1;
        }

        $this->info('Processing entities:');
        foreach ($metadata as $item) {
            $this->line($item->name);
        }

        $this->d2em->getProxyFactory()->generateProxyClasses($metadata, $directory);
        $this->info('Proxies have been created.');
    }

}
