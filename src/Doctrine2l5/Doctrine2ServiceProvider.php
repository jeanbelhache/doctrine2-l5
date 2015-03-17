<?php

namespace Doctrine2l5;

use Auth;
use Config;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityManager;

use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Cache\Cache;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Doctrine\ORM\Configuration;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\ORM\Mapping\ClassMetadataFactory;

use Doctrine2l5\EventListeners\TablePrefix;
use Doctrine2l5\Support\Repository as D2Repository;

/**
 * jeanbelhache/doctrine2-l5 - Brings Doctrine2 to Laravel 5.
 *
 * @author Jean Belhache <jeanbelhache@gmail.com>
 * @copyright Copyright (c) 2015 Belle EURL
 * @license MIT
 */
class Doctrine2ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    /**
     * Have we been configured?
     */
    private $configured = true;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot( \Doctrine\Common\Cache\Cache $d2cache )
    {
        // handle publishing of config file:
        $this->handleConfigs();

        if( !$this->configured ) {
            if( isset( $_SERVER['argv'][1] ) && $_SERVER['argv'][1] != 'vendor:publish' )
                echo "You must pubish the configuration files first: artisan vendor:publish\n";
            return;
        }

        $d2em = $this->app->make( \Doctrine\ORM\EntityManagerInterface::class );
        $d2em->getConfiguration()->setMetadataCacheImpl( $d2cache );
        $d2em->getConfiguration()->setQueryCacheImpl( $d2cache );
        $d2em->getConnection()->getConfiguration()->setResultCacheImpl( $d2cache );

        if( Config::get( 'd2doctrine.sqllogger.enabled' ) )
            $this->attachLogger( $d2em );

        if( Config::get( 'd2doctrine.auth.enabled' ) )
            $this->setupAuth();
    }



    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        if( !Config::get( 'd2doctrine' ) ) {
            if( isset( $_SERVER['argv'][1] ) && $_SERVER['argv'][1] != 'vendor:publish' )
                echo "You must pubish the configuration files first: artisan vendor:publish\n";
            $this->configured = false;
            return;
        }

        $this->registerEntityManager();
        $this->registerClassMetadataFactory();
        $this->registerConsoleCommands();
        $this->registerRepositoryFacade();
        $this->registerFacades();
    }

    /**
     * The Entity Manager - why we're all here!
     */
    private function registerEntityManager()
    {
        $this->app->singleton( EntityManagerInterface::class, function( $app ) {

            $paths = [ Config::get('d2doctrine.paths.entities') ];
            $annotations_path = base_path().'/vendor/doctrine/orm/lib/Doctrine/ORM/Mapping/Driver/DoctrineAnnotations.php';

            AnnotationRegistry::registerFile($annotations_path);

            $dconfig        = new Configuration();
            $reader         = new AnnotationReader();
            $driverImpl     = new AnnotationDriver($reader, $paths);

            $dconfig->setMetadataDriverImpl($driverImpl);

            $dconfig->setProxyDir(                 Config::get( 'd2doctrine.paths.proxies'      ) );
            $dconfig->setProxyNamespace(           Config::get( 'd2doctrine.namespaces.proxies' ) );
            $dconfig->setAutoGenerateProxyClasses( Config::get( 'd2doctrine.autogen_proxies'    ) );

            $lconfig = $this->laravelToDoctrineConfigMapper();

            //load prefix listener
            if( isset($lconfig['prefix']) && $lconfig['prefix'] && $lconfig['prefix'] !== '' ) {
                $tablePrefix = new TablePrefix( $lconfig['prefix']);
                $eventManager->addEventListener(Events::loadClassMetadata, $tablePrefix);
            }

            $dconfig->addCustomNumericFunction('SIN', '\Doctrine2l5\Query\Extensions\Sin');
            $dconfig->addCustomNumericFunction('ASIN', '\Doctrine2l5\Query\Extensions\Asin');
            $dconfig->addCustomNumericFunction('COS', '\Doctrine2l5\Query\Extensions\Cos');
            $dconfig->addCustomNumericFunction('ACOS', '\Doctrine2l5\Query\Extensions\Acos');
            $dconfig->addCustomNumericFunction('RADIANS', '\Doctrine2l5\Query\Extensions\Radians');
            $dconfig->addCustomNumericFunction('PI', '\Doctrine2l5\Query\Extensions\Pi');
            $dconfig->addCustomNumericFunction('DEGREES', '\Doctrine2l5\Query\Extensions\Degrees');

            return EntityManager::create( $lconfig, $dconfig );
        });

    }

    /**
     * Register Facades to make developers' lives easier
     */
    private function registerFacades()
    {
        // Shortcut so developers don't need to add an Alias in app/config/app.php
        \App::booting( function() {
            $loader = \Illuminate\Foundation\AliasLoader::getInstance();
            $loader->alias( 'D2EM', 'Doctrine2l5\Support\Facades\Doctrine2' );
        });
    }

    /**
     * Register Laravel console commands
     */
    private function registerConsoleCommands()
    {
        $this->commands([
            "\Doctrine2l5\Console\Generators\All",
            "\Doctrine2l5\Console\Generators\Entities",
            "\Doctrine2l5\Console\Generators\Proxies",
            "\Doctrine2l5\Console\Generators\Repositories",
            "\Doctrine2l5\Console\Schema\Create",
            "\Doctrine2l5\Console\Schema\Drop",
            "\Doctrine2l5\Console\Schema\Update",
            "\Doctrine2l5\Console\Schema\Validate",
        ]);
    }

    /**
     * Metadata Factory - mainly used by schema console commands
     */
    private function registerClassMetadataFactory()
    {
        $this->app->singleton( ClassMetadataFactory::class, function( $app ) {
            return $app[EntityManagerInterface::class]->getMetadataFactory();
        });
    }


    private function registerRepositoryFacade()
    {
        $this->app->bind( D2Repository::class, function( $app ) {
            return new D2Repository;
        });

        \App::booting( function() {
            $loader = \Illuminate\Foundation\AliasLoader::getInstance();
            $loader->alias( 'D2R', 'Doctrine2l5\Support\Facades\Doctrine2Repository' );
        });

    }

    /**
     * Attach Laravel logging to Doctrine for debugging / profiling
     */
    private function attachLogger( $d2em )
    {
        $logger = new Logger\Laravel;
        if( Config::has( 'd2doctrine.sqllogger.level' ) )
            $logger->setLevel( Config::get( 'd2doctrine.sqllogger.level' ) );

        $d2em->getConnection()->getConfiguration()->setSQLLogger( $logger );
    }

    /**
     * Set up Laravel authentication via Doctrine2 provider
     */
    private function setupAuth()
    {
        Auth::extend( 'doctrine2l5', function() {
            return new \Illuminate\Auth\Guard(
                new \Doctrine2l5\Auth\Doctrine2UserProvider(
                    \D2EM::getRepository( Config::get( 'd2doctrine.auth.entity' ) ),
                    new \Illuminate\Hashing\BcryptHasher
                ),
                \App::make('session.store')
            );
        });
    }


    /**
     * Publish configuration file
     */
    private function handleConfigs() {
        $configPath = __DIR__ . '/../config/d2doctrine.php';
        $this->publishes( [ $configPath => config_path('d2doctrine.php') ] );
        $this->mergeConfigFrom( $configPath, 'd2doctrine' );
    }


    /**
     * Convert Laravel5's database configuration to something what Doctrine2's
     * DBAL providers can use.
     *
     * @return array
     */
    private function laravelToDoctrineConfigMapper()
    {
        switch( Config::get( 'database.default' ) ) {
            case 'mysql':
                return [
                    'driver'   => 'pdo_mysql',
                    'dbname'   => Config::get( 'database.connections.mysql.database' ),
                    'user'     => Config::get( 'database.connections.mysql.username' ),
                    'password' => Config::get( 'database.connections.mysql.password' ),
                    'host'     => Config::get( 'database.connections.mysql.host'     ),
                    'charset'  => Config::get( 'database.connections.mysql.charset'  ),
                    'prefix'   => Config::get( 'database.connections.mysql.prefix'   ),
                ];
                break;

            case 'pgsql':
                return [
                    'driver'   => 'pdo_pgsql',
                    'dbname'   => Config::get( 'database.connections.pgsql.database' ),
                    'user'     => Config::get( 'database.connections.pgsql.username' ),
                    'password' => Config::get( 'database.connections.pgsql.password' ),
                    'host'     => Config::get( 'database.connections.pgsql.host'     ),
                    'charset'  => Config::get( 'database.connections.pgsql.charset'  ),
                    'prefix'   => Config::get( 'database.connections.pgsql.prefix'   ),
                ];
                break;

            case 'sqlite':
                return [
                    'driver'   => 'pdo_sqlite',
                    'path'     => Config::get( 'database.connections.sqlite.database' ),
                    'user'     => Config::get( 'database.connections.sqlite.username' ),
                    'password' => Config::get( 'database.connections.sqlite.password' ),
                    'prefix'   => Config::get( 'database.connections.sqlite.prefix'   ),
                ];
                break;

                default:
                    throw new Doctrine2l5\Exception\ImplementationNotFound( Config::get( 'database.default' ) );
        }
    }
}
