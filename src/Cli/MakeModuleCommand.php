<?php
/**
 * Copyright by David Hoeck <david.hoeck@womensbest.com>
 * Licensed under MIT
 */

namespace DavidHoeck\LaravelModular\Cli;


use Illuminate\Console\GeneratorCommand;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;



class MakeModuleCommand extends GeneratorCommand
{
    protected $signature = "make:module {name}";

    protected $description = "Generate a new module";

    protected $currentStub = "Module";


    public function __construct(Filesystem $files)
    {
        parent::__construct($files);

        $options = $this->getOptions();

        foreach($options as $option){
            $this->addOption($option[0]);
        }
    }

    /**
     * Handle the command
     * @return null
     */
    public function fire(){

        $moduleName = $this->getNameInput();

        //Check if Directory with this name already exists
        if($this->moduleDirExists($moduleName)){
            $this->error("Module with this name already exists");
            return null;
        }

        //Make Directories
        $this->makeDirectories($moduleName);

        //Make Route Files
        $this->makeRouteFiles($moduleName);



        if($this->option('with-controller')){
            $this->generateController($moduleName);
        }

    }

    /**
     * Generate a Base Controller
     * @param $moduleName
     */
    protected function generateController($moduleName){

        $fileName = studly_case(class_basename($moduleName).'Controller');

        $dirName = 'Controllers\\';

        $name = $this->parseName('Modules\\'.studly_case(ucfirst($this->getNameInput())).'\\'.$dirName.$fileName);


        if ($this->files->exists($path = $this->getPath($name))){
            $this->error($this->type.' already exists!');
            return null;
        }


        $this->currentStub = __DIR__.'/stubs/controller.stub';

        $this->makeDirectory($path);

        $this->files->put($path, $this->buildClass($name));



    }

    /**
     * Checks if the Module Folder already exists
     * @param $moduleName
     * @return bool
     */
    protected function moduleDirExists($moduleName){
        $path = app_path() . '/Modules/' . ucfirst($moduleName);

        if(is_dir($path)){
            return true;
        }
        else {
            return false;
        }

    }

    /**
     * Get the full namespace name for a given class.
     *
     * @param  string  $name
     * @return string
     */
    protected function getNamespace($name)
    {
        return trim(implode('\\', array_map('ucfirst', array_slice(explode('\\', studly_case($name)), 0, -1))), '\\');
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return $this->currentStub;
    }

    /**
     * Build the class with the given name.
     *
     * @param  string  $name
     * @return string
     */
    protected function buildClass($name)
    {
        $stub = $this->files->get($this->getStub());
        return $this->replaceName($stub, $this->getNameInput())->replaceNamespace($stub, $name)->replaceClass($stub, $name);
    }

    /**
     * Replace the placeholders
     *
     * @param  string  $stub
     * @param  string  $name
     * @return string
     */
    protected function replaceName(&$stub, $name)
    {
        $stub = str_replace('DummyTitle', $name, $stub);
        $stub = str_replace('DummyUCtitle', ucfirst(studly_case($name)), $stub);
        $stub = str_replace('DummyPrefix', '/'.strtolower($name).'/', $stub);

        return $this;
    }

    /**
     * Replace the class name for the given stub.
     *
     * @param  string  $stub
     * @param  string  $name
     * @return string
     */
    protected function replaceClass($stub, $name)
    {
        $class = class_basename($name);
        return str_replace('DummyClass', $class, $stub);
    }


    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return array(
            ['moduleName', InputArgument::REQUIRED, 'The Module Name.'],
        );
    }

    /* Get the console command options.
     *
     * @return array
     */
	protected function getOptions()
    {
        return array(
            ['with-controller', null, InputOption::VALUE_NONE, 'Create a base Controller.'],
            ['with-migration', null, InputOption::VALUE_NONE, 'Create a base Migration'],
            ['with-model', null, InputOption::VALUE_NONE, 'Create a base Model'],
            ['with-repo', null, InputOption::VALUE_NONE, 'Create a base Repository'],
            ['with-interface', null, InputOption::VALUE_NONE, 'Create a base Interface'],
        );

    }

    protected function makeDirectories($moduleName){
        $folders = array('Controllers', 'Migrations', 'Repositories', 'Interfaces', 'Translations', 'Views', 'Routes');

        foreach($folders as $folder){
            $name = $this->parseName('\\Modules\\' . ucfirst($moduleName) . '\\' . ucfirst($folder . '\\'));
           $this->makeDirectory($this->getPath($name));
        }

        $this->info('Created Folders for Module: ' . $moduleName);
    }

    protected function makeRouteFiles($moduleName){
        $routeFileNames = array('api', 'web');



        foreach($routeFileNames as $fileName){

            $name = $this->parseName('Modules\\'.studly_case(ucfirst($moduleName)).'\\'.$fileName);

            if ($this->files->exists($path = $this->getPath($name))){
                $this->error($this->type.' already exists!');
                return null;
            }

            $this->currentStub = __DIR__.'/stubs/routes/'.$fileName.'.stub';

            $this->makeDirectory($path);

            $this->files->put($path, $this->buildClass($name) );
        }

        $this->info('Created Route Files for Module: ' . $moduleName);

    }



}