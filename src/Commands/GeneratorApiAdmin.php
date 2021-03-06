<?php

namespace Amet\SimpleAdminAPI\Commands;

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
    protected $name = 'simple_admin_api:generate';

    private $models = [];
    private $task;
    private $list_models;
    private $prefix;
    private $models_params;
    private $framework;
    private $version;

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
        $this->task = $this->choice('Which Task would you like to generate ?', ["Admin","API","Admin & API"]);
        $this->list_models = $this->choice('Which Models would you like to generate ?', array_merge(["All"],$this->models ));
        $this->generate_params();
        if ($this->task == "Admin" || $this->task == "Admin & API") {
            $this->generateAdmin();
        }
        if ($this->task == "API" || $this->task == "Admin & API") {
            $this->generateAPI();
        }
    }   

    private function getStubPath()
    {
        return __DIR__.'/../stubs';
    }

    private function generate_params()
    {
        if (!file_exists(resource_path('views/simple_admin_api/'))) {
            mkdir(resource_path('views/simple_admin_api/'),0775);
        }
        if (!file_exists(app_path('Http/Controllers/SimpleAdmin/'))) {
            mkdir(app_path('Http/Controllers/SimpleAdmin/'),0775);
        }
        if (!file_exists(app_path('Http/Controllers/SimpleAPI/'))) {
            mkdir(app_path('Http/Controllers/SimpleAPI/'),0775);
        }
        $models_params = [];
        $models_to_generate = [$this->list_models];
        if ($this->list_models == "All") {
            $models_to_generate = $this->models;
        }
        // print_r($models_to_generate);
        foreach ($models_to_generate as $key => $to_generate) {
            $model =  "App\ORM\\".$to_generate;
            $admin_params = $api_params = [];
            $file_name = str_plural(strtolower(snake_case($to_generate)));
            if ($this->task == "Admin" || $this->task == "Admin & API") {
                $admin_params = [
                    'controller_path' => app_path('Http/Controllers/SimpleAdmin/'.$to_generate.'.php'),
                    'view_path' => resource_path('views/simple_admin_api/'.$file_name),
                ];
            }
            if (!file_exists(resource_path('views/simple_admin_api/'.$file_name))) {
                mkdir(resource_path('views/simple_admin_api/'.$file_name),0775);
            }
            if ($this->task == "API" || $this->task == "Admin & API") {
                $api_params = [
                    'controller_path' => app_path('Http/Controllers/SimpleAPI/'.$to_generate.'.php'),
                ];
            }
            $get_model = new $model;
            $get_column = $get_model->getSelectedColumn();
            $default_key = $get_model->getDefaultKey();
            $table = $get_model->getTableName();
            $columns = [];
            if ($this->task != "API") {
                $this->info("Select Type for ".$to_generate." Model");
                foreach ($get_column as $key => $column) {
                    if($column != "created_at" && $column != "updated_at" && $column != "deleted_at" && $column != $default_key) 
                    {
                        $id = $file_name;
                        $type = $this->choice("Selete type of $column",
                            ["text","number","password","email","select","radio","checkbox","textarea","file","date","color"]);
                        if ($type == "textarea") {
                            $id = $this->choice("Selete type of $column editor",
                            ["ckeditor","tinymce","no-editor"]);
                        }
                        $columns[] = [
                            'name' => $column,
                            'type' => $type,
                            'id' => $id,
                            ];
                    }
                }
            }
            $models_params[] = [
                'model' => $to_generate,
                'table' => $table,
                'alias' => $file_name,
                'default_key' => $default_key,
                'column' => $columns,
                'admin_params' => $admin_params, 
                'api_params' => $api_params, 
            ];
        }
        // print_r($models_params);
        $this->models_params = $models_params;
    }

    private function generateAPI()
    {
        foreach ($this->models_params as $key => $models_params) {
            $route_template = "Route::resource('".str_plural(strtolower(snake_case($models_params['model'])))."', 'SimpleAPI\\".ucfirst(camel_case($models_params['model'])).'Controller'."', ['as' => env('APP_API_PREFIX','simple_api')]);";
            if ($this->framework == "lumen") {
                $route_template = '$app->resource("'.str_plural(strtolower(snake_case($models_params['model']))).'", "SimpleAPI\\'.ucfirst(camel_case($models_params['model']))."Controller".'",["as" => env("APP_API_PREFIX","simple_api")]);';
            }
            $route_path = base_path().'/routes/api.php';
            // if (preg_match(pattern, subject))
            if (!version_compare($this->version, '5.2')) {
                $route_path = app_path().'/Http/routes.php';
            }
            $template = $this->getStubPath().'/ControllerApi.stub';
            $file_name = ucfirst(camel_case($models_params['model'])).'Controller'.'.php';
            $path_controller = app()->path().'/Http/Controllers/SimpleAPI/';
            try {
                $fh = fopen($template,'r+');
                $content = "";
                $line_number = 1;
                while(!feof($fh)) {
                    $line = fgets($fh);
                    $line = str_replace("sampleController", ucfirst(camel_case($models_params['model'])).'Controller', $line);
                    $line = str_replace("samples",str_plural(strtolower(snake_case($models_params['model']))), $line);
                    $line = str_replace("Model",ucfirst(camel_case($models_params['model'])), $line);
                    $content .= $line;
                    $line_number++;
                }
                
                fclose($fh);
                
                file_put_contents($path_controller.$file_name, $content);
                $this->info('Created Controller: '.$file_name);

                
                file_put_contents($route_path, PHP_EOL.$route_template.PHP_EOL , FILE_APPEND | LOCK_EX);
                $this->info('Add Route: '.$route_path);
            } catch (\Exception $e) {
                $this->error($e->getMessage());
            }
        }
    }
    private function generateAdmin()
    {
        foreach ($this->models_params as $key => $models_params) {
            // print_r($models_params);
            //generate controller
            $models_params['admin_params']['controller_path'];
            $route_template = "Route::resource(env('APP_ADMIN_PREFIX','simple_admin').'/".str_plural(strtolower(snake_case($models_params['model'])))."', 'SimpleAdmin\\".ucfirst(camel_case($models_params['model'])).'Controller'."', ['as' => env('APP_ADMIN_PREFIX','simple_admin_api')]);";
            if ($this->framework == "lumen") {
                $route_template = '$app->resource(env("APP_ADMIN_PREFIX","simple_admin")."/'.str_plural(strtolower(snake_case($models_params['model']))).'", "SimpleAdmin\\'.ucfirst(camel_case($models_params['model']))."Controller".'",["as" => env("APP_ADMIN_PREFIX","simple_admin_api")]);';
            }
            $route_path = base_path().'/routes/web.php';
            // if (preg_match(pattern, subject))
            if (!version_compare($this->version, '5.2')) {
                $route_path = app_path().'/Http/routes.php';
            }
            $template = $this->getStubPath().'/Controller.stub';
            $file_name = ucfirst(camel_case($models_params['model'])).'Controller'.'.php';
            $path_controller = app()->path().'/Http/Controllers/SimpleAdmin/';
            $path_view = resource_path('views/'.str_plural(strtolower(snake_case($models_params['model']))));
            try {
                $fh = fopen($template,'r+');
                $content = "";
                $line_number = 1;
                while(!feof($fh)) {
                    $line = fgets($fh);
                    $line = str_replace("sampleController", ucfirst(camel_case($models_params['model'])).'Controller', $line);
                    $line = str_replace("samples",str_plural(strtolower(snake_case($models_params['model']))), $line);
                    $line = str_replace("prefix",'simple_admin_api', $line);
                    $line = str_replace("Model",ucfirst(camel_case($models_params['model'])), $line);
                    if ($models_params['model'] == "User") {
                        $line = str_replace("rules_access",'manage_user', $line);
                    }
                    foreach ($models_params['column'] as $key => $column) {
                        $file_template_flow  = "\t\t".'if ($request->'.$column['name'].') {'.PHP_EOL;
                        $file_template_flow .= "\t\t\t".'$path = $request->'.$column['name'].'->store(\'images\', \'public\');'.PHP_EOL;
                        $file_template_flow .= "\t\t\t".'$input[\''.$column['name'].'\'] = $path;'.PHP_EOL;
                        $file_template_flow .= "\t\t".'}'.PHP_EOL;
                        if ($column['type'] == "file") {
                            $line = str_replace("//CUSTOM FLOW STORE",$line.PHP_EOL.$file_template_flow , $line);
                            $line = str_replace("//CUSTOM FLOW UPDATE",$line.PHP_EOL.$file_template_flow , $line);
                        }
                    }
                    $content .= $line;
                    $line_number++;
                }
                
                fclose($fh);
                if (!file_exists($path_view)) {
                    mkdir($path_view,0777,true);
                }
                file_put_contents($path_controller.$file_name, $content);
                $this->info('Created Controller: '.$file_name);

                
                file_put_contents($route_path, PHP_EOL.$route_template.PHP_EOL , FILE_APPEND | LOCK_EX);
                $this->info('Add Route: '.$route_path);
                $this->generateView($models_params);
            } catch (\Exception $e) {
                $this->error($e->getMessage());
            }
        }
    }

    private function generateView($models_params)
    {
        $this->generateIndexView($models_params);
        $this->generateCreateView($models_params);
        $this->generateEditView($models_params);
        $this->generateShowView($models_params);
        $this->addMenu($models_params);
    }

    private function addMenu($models_params)
    {
        if ($this->confirm("Would you like add ".$models_params['model']." link to sidebar menu")) {
            $template = $this->getStubPath().'/admin/menu.stub';
            $fh = fopen($template,'r+');
            $content = "";
            $line_number = 1;
            while(!feof($fh)) {
                $line = fgets($fh);
                $line = str_replace('$MODEL', $models_params['model'], $line);
                $line = str_replace('prefix', "simple_admin_api", $line);
                $line = str_replace('samples', $models_params['alias'], $line);
                $content .= $line;
                $line_number++;
            }
            fclose($fh);
            file_put_contents(resource_path('views/simple_admin_api/menu.blade.php'), PHP_EOL.$content, FILE_APPEND | LOCK_EX);
        }
    }

    private function generateShowView($models_params)
    {
        $template = $this->getStubPath().'/admin/show.stub';
        $path_view = $models_params['admin_params']['view_path'];

        $fh = fopen($template,'r+');
        $content = "";
        $line_number = 1;
        while(!feof($fh)) {
            $line = fgets($fh);
            $line = str_replace('$MODEL', "Show ". $models_params['model'], $line);
            $line = str_replace('samples', $models_params['alias'], $line);
            $line = str_replace('default_key', $models_params['default_key'], $line);
            if ($line_number == 27) {
                foreach ($models_params['column'] as $key => $column) {
                    $line .="\t\t\t\t\t\t\t".'<div class="form-group form-float">'.PHP_EOL;
                    $line .="\t\t\t\t\t\t\t\t".'<div class="form-line">'.PHP_EOL;
                    $line .="\t\t\t\t\t\t\t\t\t".'<h3 class="card-inside-title">'.title_case(str_replace("_", " ", $column['name'])).'</h3>'.PHP_EOL;
                    $line .="\t\t\t\t\t\t\t\t\t".'<p>{!! $'.$models_params['alias'].'->'.$column['name'].' !!}</>'.PHP_EOL;
                    $line .="\t\t\t\t\t\t\t\t".'</div>'.PHP_EOL;
                    $line .="\t\t\t\t\t\t\t".'</div>'.PHP_EOL;
                }
            }
            $content .= $line;
            $line_number++;
        }

        fclose($fh);

        file_put_contents($path_view.'/'.'show.blade.php', $content);
    }
    private function generateEditView($models_params)
    {
        $template = $this->getStubPath().'/admin/create.stub';
        $path_view = $models_params['admin_params']['view_path'];

        $fh = fopen($template,'r+');
        $content = "";
        $line_number = 1;
        while(!feof($fh)) {
            $line = fgets($fh);
            $line = str_replace('$MODEL', "Create ". $models_params['model'], $line);
            if ($line_number == 25) {
                $line .="\t\t\t\t\t\t".'<form enctype="multipart/form-data" method="post" action="{!! route(env(\'APP_ADMIN_PREFIX\',\'simple_admin\').".'.$models_params['alias'].'.update", ["'.$models_params['default_key'].'" => $'.$models_params['alias'].'->'.$models_params['default_key'].']) !!}">'.PHP_EOL;
                $line .="\t\t\t\t\t\t\t".'<input name="_method" type="hidden" value="PUT"><input name="_token" type="hidden" value="{!! csrf_token() !!}">'.PHP_EOL;
                foreach ($models_params['column'] as $key => $column) {
                    if ($column['name'] == "remember_token") {
                        
                    } else
                    if ($column['type'] == "textarea") {
                        $class = "form-control ".$column['id'];
                        $line .="\t\t\t\t\t\t\t".'<div class="form-group form-float">'.PHP_EOL;
                        $line .="\t\t\t\t\t\t\t\t".'<div class="form-line">'.PHP_EOL;
                        $line .="\t\t\t\t\t\t\t\t\t".'<h3 class="card-inside-title">'.title_case(str_replace("_", " ", $column['name'])).'</h3>'.PHP_EOL;
                        $line .="\t\t\t\t\t\t\t\t\t".'<textarea rows="9" name="'.$column['name'].'" class="'.$class.'" />{!! $'.$models_params['alias'].'->'.$column['name'].' !!}</textarea>'.PHP_EOL;
                        $line .="\t\t\t\t\t\t\t\t".'</div>'.PHP_EOL;
                        $line .="\t\t\t\t\t\t\t".'</div>'.PHP_EOL;
                    } else
                    if ($column['type'] == 'radio' || $column['type'] == 'checkbox') {
                        $line .="\t\t\t\t\t\t\t".'<h3 class="card-inside-title">'.title_case(str_replace("_", " ", $column['name'])).'</h3>'.PHP_EOL;
                        $line .="\t\t\t\t\t\t\t\t".'<div class="demo-radio-button">'.PHP_EOL;
                        $line .="\t\t\t\t\t\t\t\t\t".'<input name="'.$column['name'].'" type="'.$column['type'].'" id="'.$column['type'].'_1" checked />'.PHP_EOL;
                        $line .="\t\t\t\t\t\t\t\t\t".'<label for="'.$column['type'].'_1">'.ucfirst($column['type']).' - 1</label>'.PHP_EOL;
                        $line .="\t\t\t\t\t\t\t\t\t".'<input name="'.$column['name'].'" type="'.$column['type'].'" id="'.$column['type'].'_2" />'.PHP_EOL;
                        $line .="\t\t\t\t\t\t\t\t\t".'<label for="'.$column['type'].'_2">'.ucfirst($column['type']).' - 2</label>'.PHP_EOL;
                        $line .="\t\t\t\t\t\t\t\t".'</div>'.PHP_EOL;
                    } else
                    if ($column['type'] != "select") {
                        $line .="\t\t\t\t\t\t\t".'<div class="form-group form-float">'.PHP_EOL;
                        $line .="\t\t\t\t\t\t\t\t".'<div class="form-line">'.PHP_EOL;
                        $line .="\t\t\t\t\t\t\t\t\t".'<h3 class="card-inside-title">'.title_case(str_replace("_", " ", $column['name'])).'</h3>'.PHP_EOL;
                        $line .="\t\t\t\t\t\t\t\t\t".'<input type="'.$column['type'].'" name="'.$column['name'].'"  id="'.$column['name'].'" class="form-control" value="{!! $'.$models_params['alias'].'->'.$column['name'].' !!}">'.PHP_EOL;
                        $line .="\t\t\t\t\t\t\t\t".'</div>'.PHP_EOL;
                        $line .="\t\t\t\t\t\t\t".'</div>'.PHP_EOL;
                    } else {
                        $line .="\t\t\t\t\t\t\t".'<div class="form-group form-float">'.PHP_EOL;
                        $line .="\t\t\t\t\t\t\t\t".'<div class="form-line">'.PHP_EOL;
                        $line .="\t\t\t\t\t\t\t\t\t".'<h3 class="card-inside-title">'.title_case(str_replace("_", " ", $column['name'])).'</h3>'.PHP_EOL;
                        $line .="\t\t\t\t\t\t\t\t\t".'<select name="'.$column['name'].'" id="'.$column['name'].'" class="form-control">'.PHP_EOL;
                        $line .="\t\t\t\t\t\t\t\t\t\t".'<option value="1">Value 1</option>'.PHP_EOL;
                        $line .="\t\t\t\t\t\t\t\t\t\t".'<option value="2">Value 2</option>'.PHP_EOL;
                        $line .="\t\t\t\t\t\t\t\t\t\t".'<option value="3">Value 3</option>'.PHP_EOL;
                        $line .="\t\t\t\t\t\t\t\t\t".'</select>'.PHP_EOL;
                        
                        $line .="\t\t\t\t\t\t\t\t".'</div>'.PHP_EOL;
                        $line .="\t\t\t\t\t\t\t".'</div>'.PHP_EOL;
                    }
                }
                $line .="\t\t\t\t\t\t".'</form>'.PHP_EOL;
            }
            $content .= $line;
            $line_number++;
        }

        fclose($fh);

        file_put_contents($path_view.'/'.'edit.blade.php', $content);

    }

    private function generateCreateView($models_params)
    {
        $template = $this->getStubPath().'/admin/create.stub';
        $path_view = $models_params['admin_params']['view_path'];

        $fh = fopen($template,'r+');
        $content = "";
        $line_number = 1;
        while(!feof($fh)) {
            $line = fgets($fh);
            $line = str_replace('$MODEL', "Create ". $models_params['model'], $line);
            if ($line_number == 25) {
                $line .="\t\t\t\t\t\t".'<form enctype="multipart/form-data" method="post" action="{!! route(env(\'APP_ADMIN_PREFIX\',\'simple_admin\').".'.$models_params['alias'].'.store") !!}">'.PHP_EOL;
                $line .="\t\t\t\t\t\t\t".'<input name="_token" type="hidden" value="{!! csrf_token() !!}">'.PHP_EOL;
                foreach ($models_params['column'] as $key => $column) {
                    if ($column['name'] == "remember_token") {
                        
                    } else
                    if ($column['type'] == "textarea") {
                        $class = "form-control ".$column['id'];
                        $line .="\t\t\t\t\t\t\t".'<div class="form-group form-float">'.PHP_EOL;
                        $line .="\t\t\t\t\t\t\t\t".'<div class="form-line">'.PHP_EOL;
                        $line .="\t\t\t\t\t\t\t\t\t".'<h3 class="card-inside-title">'.title_case(str_replace("_", " ", $column['name'])).'</h3>'.PHP_EOL;
                        $line .="\t\t\t\t\t\t\t\t\t".'<textarea rows="9" name="'.$column['name'].'" class="'.$class.'"  /></textarea>'.PHP_EOL;
                        $line .="\t\t\t\t\t\t\t\t".'</div>'.PHP_EOL;
                        $line .="\t\t\t\t\t\t\t".'</div>'.PHP_EOL;
                    } else
                    if ($column['type'] == 'radio' || $column['type'] == 'checkbox') {
                        $line .="\t\t\t\t\t\t\t".'<h3 class="card-inside-title">'.title_case(str_replace("_", " ", $column['name'])).'</h3>'.PHP_EOL;
                        $line .="\t\t\t\t\t\t\t\t".'<div class="demo-radio-button">'.PHP_EOL;
                        $line .="\t\t\t\t\t\t\t\t\t".'<input name="'.$column['name'].'" type="'.$column['type'].'" id="'.$column['type'].'_1" checked />'.PHP_EOL;
                        $line .="\t\t\t\t\t\t\t\t\t".'<label for="'.$column['type'].'_1">'.ucfirst($column['type']).' - 1</label>'.PHP_EOL;
                        $line .="\t\t\t\t\t\t\t\t\t".'<input name="'.$column['name'].'" type="'.$column['type'].'" id="'.$column['type'].'_2" />'.PHP_EOL;
                        $line .="\t\t\t\t\t\t\t\t\t".'<label for="'.$column['type'].'_2">'.ucfirst($column['type']).' - 2</label>'.PHP_EOL;
                        $line .="\t\t\t\t\t\t\t\t".'</div>'.PHP_EOL;
                    } else
                    if ($column['type'] != "select") {
                        $line .="\t\t\t\t\t\t\t".'<div class="form-group form-float">'.PHP_EOL;
                        $line .="\t\t\t\t\t\t\t\t".'<div class="form-line">'.PHP_EOL;
                        $line .="\t\t\t\t\t\t\t\t\t".'<h3 class="card-inside-title">'.title_case(str_replace("_", " ", $column['name'])).'</h3>'.PHP_EOL;
                        $line .="\t\t\t\t\t\t\t\t\t".'<input type="'.$column['type'].'" name="'.$column['name'].'"  id="'.$column['name'].'" class="form-control">'.PHP_EOL;
                        $line .="\t\t\t\t\t\t\t\t".'</div>'.PHP_EOL;
                        $line .="\t\t\t\t\t\t\t".'</div>'.PHP_EOL;
                    } else {
                        $line .="\t\t\t\t\t\t\t".'<div class="form-group form-float">'.PHP_EOL;
                        $line .="\t\t\t\t\t\t\t\t".'<div class="form-line">'.PHP_EOL;
                        $line .="\t\t\t\t\t\t\t\t\t".'<h3 class="card-inside-title">'.title_case(str_replace("_", " ", $column['name'])).'</h3>'.PHP_EOL;
                        $line .="\t\t\t\t\t\t\t\t\t".'<select name="'.$column['name'].'" id="'.$column['name'].'" class="form-control">'.PHP_EOL;
                        $line .="\t\t\t\t\t\t\t\t\t\t".'<option value="1">Value 1</option>'.PHP_EOL;
                        $line .="\t\t\t\t\t\t\t\t\t\t".'<option value="2">Value 2</option>'.PHP_EOL;
                        $line .="\t\t\t\t\t\t\t\t\t\t".'<option value="3">Value 3</option>'.PHP_EOL;
                        $line .="\t\t\t\t\t\t\t\t\t".'</select>'.PHP_EOL;
                        
                        $line .="\t\t\t\t\t\t\t\t".'</div>'.PHP_EOL;
                        $line .="\t\t\t\t\t\t\t".'</div>'.PHP_EOL;
                    }
                }
                $line .="\t\t\t\t\t\t".'</form>'.PHP_EOL;
            }
            $content .= $line;
            $line_number++;
        }

        fclose($fh);

        file_put_contents($path_view.'/'.'create.blade.php', $content);

    }
    private function generateIndexView($models_params){
        $template = $this->getStubPath().'/admin/index.stub';
        $path_view = $models_params['admin_params']['view_path'];

        $fh = fopen($template,'r+');
        $content = "";
        $line_number = 1;
        while(!feof($fh)) {
            $line = fgets($fh);
            $line = str_replace('$MODEL', $models_params['model'], $line);
            $line = str_replace('$link_add', "{!! route(env('APP_ADMIN_PREFIX','simple_admin').'.".$models_params['alias'].".create') !!}", $line);
            $line = str_replace("samples",str_plural(strtolower(snake_case($models_params['model']))), $line);
            $line = str_replace("Model",ucfirst(camel_case($models_params['model'])), $line);
            if ($line_number == 28) {
                foreach ($models_params['column'] as $key => $column) {
                    if ($column['name'] == "password" || $column['name'] == "remember_token" ) {

                    } else {
                        $line .="\t\t\t\t\t\t\t\t\t\t"."<th>".title_case(str_replace("_", " ", $column['name']))."</th>".PHP_EOL;
                    }
                }
            }
            if ($line_number == 32) {
                $form_delete = '<form method="POST" action="{!! route(env(\'APP_ADMIN_PREFIX\',\'simple_admin\').".'.$models_params['alias'].'.destroy", [\''.$models_params['default_key'].'\' => $'.str_singular($models_params['alias']).'->'.$models_params['default_key'].']) !!}" accept-charset="UTF-8"><input name="_method" type="hidden" value="DELETE"><input name="_token" type="hidden" value="{!! csrf_token() !!}">';
                $menu_row  = "\t\t\t\t\t\t\t\t\t\t\t".'<ul class="header-dropdown m-r--5" style="list-style-type: none;float: right;">'.PHP_EOL;
                $menu_row .= "\t\t\t\t\t\t\t\t\t\t\t".$form_delete.PHP_EOL;
                $menu_row .= "\t\t\t\t\t\t\t\t\t\t\t\t".'<li class="dropdown">'.PHP_EOL;
                $menu_row .= "\t\t\t\t\t\t\t\t\t\t\t\t\t".'<a href="javascript:void(0).PHP_EOL;" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">'.PHP_EOL;
                $menu_row .= "\t\t\t\t\t\t\t\t\t\t\t\t\t\t".'<i class="material-icons">more_vert</i>'.PHP_EOL;
                $menu_row .= "\t\t\t\t\t\t\t\t\t\t\t\t\t".'</a>'.PHP_EOL;
                $menu_row .= "\t\t\t\t\t\t\t\t\t\t\t\t\t".'<ul class="dropdown-menu pull-right">'.PHP_EOL;
                $menu_row .= "\t\t\t\t\t\t\t\t\t\t\t\t\t\t".'<li><a href="{!! route(env(\'APP_ADMIN_PREFIX\',\'simple_admin\').".'.$models_params['alias'].'.show", [\''.$models_params['default_key'].'\' => $'.str_singular($models_params['alias']).'->'.$models_params['default_key'].']) !!}"><i class="material-icons">remove_red_eye</i>Show</a></li>'.PHP_EOL;
                $menu_row .= "\t\t\t\t\t\t\t\t\t\t\t\t\t\t".'<li><a href="{!! route(env(\'APP_ADMIN_PREFIX\',\'simple_admin\').".'.$models_params['alias'].'.edit", [\''.$models_params['default_key'].'\' => $'.str_singular($models_params['alias']).'->'.$models_params['default_key'].']) !!}"><i class="material-icons">mode_edit</i>Edit</a></li>'.PHP_EOL;
                $menu_row .= "\t\t\t\t\t\t\t\t\t\t\t\t\t\t".'<li>'.PHP_EOL;
                $menu_row .= "\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t".'<a onclick="$(this).closest(\'form\').submit()"><i class="material-icons">delete</i>Delete</a>'.PHP_EOL;
                $menu_row .= "\t\t\t\t\t\t\t\t\t\t\t\t\t\t".'</li>'.PHP_EOL;
                $menu_row .= "\t\t\t\t\t\t\t\t\t\t\t\t\t".'</ul>'.PHP_EOL;
                $menu_row .= "\t\t\t\t\t\t\t\t\t\t\t\t".'</li>'.PHP_EOL;
                $menu_row .= "\t\t\t\t\t\t\t\t\t\t\t\t".'</form>'.PHP_EOL;
                $menu_row .= "\t\t\t\t\t\t\t\t\t\t\t".'</ul>'.PHP_EOL;

                $line .="\t\t\t\t\t\t\t\t"."@foreach($".$models_params['alias']." as $".str_singular($models_params['alias']).")".PHP_EOL;
                $line .="\t\t\t\t\t\t\t\t\t"."<tr>".PHP_EOL;
                foreach ($models_params['column'] as $key => $column) {
                    if ($column['name'] == "password" || $column['name'] == "remember_token" ) {

                    } else {
                        $line .="\t\t\t\t\t\t\t\t\t\t"."<td>{!! $".str_singular($models_params['alias']).'->'.$column['name']." !!}</td>".PHP_EOL;
                    }
                }
                $line .="\t\t\t\t\t\t\t\t\t\t"."<td>".PHP_EOL;
                $line .= $menu_row;
                $line .="\t\t\t\t\t\t\t\t\t\t"."</td>".PHP_EOL;
                $line .="\t\t\t\t\t\t\t\t\t"."</tr>".PHP_EOL;
                $line .="\t\t\t\t\t\t\t\t"."@endforeach".PHP_EOL;
            }
            $content .= $line;
            $line_number++;
        }
        
        fclose($fh);

        file_put_contents($path_view.'/'.'index.blade.php', $content);
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