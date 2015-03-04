<?php

namespace Doctrine2l5\Console\Schema;


use Illuminate\Console\Command as LaravelCommand;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Mapping\ClassMetadataFactory;

/**
 * jeanbelhache/doctrine2-l5 - Brings Doctrine2 to Laravel 5.
 *
 * @author Jean Belhache <jeanbelhache@gmail.com>
 * @copyright Copyright (c) 2015 Belle EURL
 * @license MIT
 */
class Create extends LaravelCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'd2:schema:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create the database schema from models';


    /**
     * The schema tool.
     *
     * @var \Doctrine\ORM\Tools\SchemaTool
     */
    private $tool;

    /**
     * The class metadata factory
     *
     * @var \Doctrine\ORM\Tools\SchemaTool
     */
    private $metadata;

    public function __construct(SchemaTool $tool, ClassMetadataFactory $metadata)
    {
        parent::__construct();
        $this->tool = $tool;
        $this->metadata = $metadata;
    }


    public function fire()
    {
        if( $this->option('sql') ) {
            $this->info('Outputting create query:' . PHP_EOL);

            $sql = $this->tool->getCreateSchemaSql($this->metadata->getAllMetadata());
            $this->info(implode(';'.PHP_EOL, $sql) . ';');

        } else if( $this->option('commit') ) {
            $this->info('Creating database schema...');
            $this->tool->createSchema($this->metadata->getAllMetadata());
            $this->info('Schema has been created!');
        } else {
            $this->comment( "Warning: this command can cause data loss. Run with --sql or --commit." );
        }
    }

    protected function getOptions()
    {
        return [
            ['sql', false, InputOption::VALUE_NONE, 'Dumps SQL query and does not execute creation.'],
            ['commit', false, InputOption::VALUE_NONE, 'Executes database schema creation.']
        ];
    }

}
