<?php
/**
 * Copyright by David Hoeck <david.hoeck@womensbest.com>
 * Licensed under MIT
 */

namespace DavidHoeck\LaravelModular;


use DavidHoeck\LaravelModular\Cli\MakeModuleCommand;
use DavidHoeck\LaravelModular\Cli\ModuleCommands;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;

class ModuleServiceProvider extends ServiceProvider
{
    /**
     * All module files
     * @var
     */
    protected $moduleFiles;

    const MODULES_FOLDER_NAME = "Modules";

    public function register(){

        $this->moduleFiles = new Filesystem();
        $this->registerCliCommands();

    }
    public function boot(){

        if( $this->modulesFolderExists() ){
            $modules = $this->getModules();

            foreach($modules as $m){
                $this->includeModuleFiles($m);
            }
        }

    }

    /**
     * Register CLI Commands to generate
     * Modules, Controllers, Repositories, Interfaces and Models
     */
    protected function registerCliCommands(){

        $this->commands('modules.make');

        $bind_method = method_exists($this->app, 'bindShared') ? 'bindShared' : 'singleton';

        $this->app->{$bind_method}('modules.make', function($app) {
            return new MakeModuleCommand($this->moduleFiles);
        });
    }

    /**
     * Check if modules folder exists
     * @return bool
     */
    private function modulesFolderExists(){
        return is_dir( app_path() . '/' . self::MODULES_FOLDER_NAME . '/');
    }

    /**
     * Get all active modules
     * @return array
     */
    private function getModules(){
        return Config::get("modules.active") ?: array_map('class_basename', $this->moduleFiles->directories(app_path().'/Modules/'));
    }

    private function includeModuleFiles($module){


        if (!$this->app->routesAreCached()) {

            //API and WEB Routes

            $webRoutes = app_path() . '/Modules/' . $module . '/web.php';
            $apiRoutes = app_path() . '/Modules/' . $module . '/api.php';

            if($this->moduleFiles->exists($webRoutes) ) {
                include $webRoutes;
            }

            if($this->moduleFiles->exists($apiRoutes) ) {
                include $apiRoutes;
            }

        }

        $helperFile   = app_path().'/Modules/'.$module.'/helper.php';
        $viewFiles    = app_path().'/Modules/'.$module.'/Views';
        $transFiles   = app_path().'/Modules/'.$module.'/Translations';
        $migrationFiles = app_path().'/Modules/'.$module.'/Migrations';


        if($this->moduleFiles->exists($helperFile)) include_once $helperFile;
        if($this->moduleFiles->isDirectory($viewFiles)) $this->loadViewsFrom($viewFiles, $module);
        if($this->moduleFiles->isDirectory($transFiles)) $this->loadTranslationsFrom($transFiles, $module);
        if($this->moduleFiles->isDirectory($migrationFiles)) $this->loadMigrationsFrom($migrationFiles, $module);


    }
}