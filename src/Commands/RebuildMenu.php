<?php

namespace Amet\SimpleAdminAPI\Commands;

use File;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class RebuildMenu extends Command
{
    protected $name = 'simple_admin_api:rebuild_menu';
    protected $description = 'Rebuild Sidebar Menu';

    public function handle()
    {
        $v = app()->version();
        $version = explode(" ", $v);
        $this->version = $version[0];
        if ($version[0] == "Lumen") {
            $this->framework = "lumen";
            $this->version =  str_replace(")", "", str_replace("(", "", $version[1]));
        }

        $model_path = app_path('ORM');
        $models = scandir($model_path);
        
        foreach ($models as $key => $model) {
            if ($model != "." && $model != "..") {
                $this->models[] = str_replace(".php", "", $model);
            }
        }
        if (!count($this->models)) {
            $this->error("You don't have any models");
            if ($this->confirm('Do you want to generate Model with SIMPLE_ORM Generator')) {
                $this->call('simple_orm:interactive');
            } else {
                exit;
            }
        }
        $this->list_models = $this->choice('Which Models would you like to generate ?', array_merge(["All"],$this->models ));
        $this->rebuild_menu();

    }
    
    private function rebuild_menu()
    {
    	$models_to_generate = [$this->list_models];
        if ($this->list_models == "All") {
            $models_to_generate = $this->models;
            $content  = '<li class="active">'.PHP_EOL;
            $content .= "\t".'<a href="/{!! env(\'APP_ADMIN_PREFIX\',\'simple_admin\') !!}/dashboard">'.PHP_EOL;
            $content .= "\t\t".'<i class="material-icons">home</i>'.PHP_EOL;
            $content .= "\t\t".'<span>Home</span>'.PHP_EOL;
            $content .= "\t".'</a>'.PHP_EOL;
            $content .= '</li>'.PHP_EOL;
            file_put_contents(resource_path('views/simple_admin_api/menu.blade.php'), PHP_EOL.$content);
        }

        $models_params = [];
    	foreach ($models_to_generate as $key => $model) {
    		$model_name = ucfirst(camel_case($model));
    		$alias = str_plural(strtolower(snake_case($model)));
    		$this->addMenu($model_name,$alias);
    	}
    }

    private function addMenu($model,$alias)
    {
            $template = $this->getStubPath().'/admin/menu.stub';
            $fh = fopen($template,'r+');
            
            $content = "";
            $line_number = 1;
            while(!feof($fh)) {
                $line = fgets($fh);
                $line = str_replace('$MODEL', $model, $line);
                $line = str_replace('samples', $alias, $line);
                $content .= $line;
                $line_number++;
            }
            fclose($fh);
            file_put_contents(resource_path('views/simple_admin_api/menu.blade.php'), PHP_EOL.$content, FILE_APPEND | LOCK_EX);
            $this->info('Menu Added: '.$model);

    }

    private function getStubPath()
    {
        return __DIR__.'/../stubs';
    }
}