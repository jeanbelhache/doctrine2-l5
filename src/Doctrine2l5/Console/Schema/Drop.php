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
class Drop extends LaravelCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'd2:schema:drop';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Drop the database schema';


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
        $sql = $this->tool->getDropSchemaSQL($this->metadata->getAllMetadata());

        if( empty($sql) ) {
            $this->error('Current models do not exist in schema.');
            return;
        }

        if( $this->option('sql') ) {
            $this->info('Outputting drop query:'.PHP_EOL);
            $this->line(implode(';' . PHP_EOL, $sql) . ';');
        } else if( $this->option('commit') ) {
            $this->info('Dropping database schema....');
            $this->tool->dropSchema($this->metadata->getAllMetadata());
            $this->info('Schema has been dropped!');
        } else {
            $this->comment( "Warning: this command can cause data loss. Run with --sql or --commit." );
        }
    }

    protected function getOptions()
    {
        return [
            ['sql', false, InputOption::VALUE_NONE, 'Dumps SQL query and does not execute drop.'],
            ['commit', false, InputOption::VALUE_NONE, 'Executes database schema drop.']
        ];
    }

}
