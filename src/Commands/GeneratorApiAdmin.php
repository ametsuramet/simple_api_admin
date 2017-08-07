<?php

namespace Amet\SimpleORM\Commands;

use File;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class GeneratorApiAdmin extends Command
{

    
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'simple_api_admin:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publishes Simple Admin & API';

    /**
     * Execute the command.
     *
     * @return void
     */
    public function handle()
    {
        
        

    }   

    private function getStubPath()
    {
        return __DIR__.'/../stubs';
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            // ['name', InputArgument::REQUIRED, 'Model Name.'],
        ];
    }

    protected function getOptions()
    {
        return [
            // ['soft_delete', null, InputOption::VALUE_OPTIONAL, 'Soft Delete option.', null],
        ];
 
 
    }


}