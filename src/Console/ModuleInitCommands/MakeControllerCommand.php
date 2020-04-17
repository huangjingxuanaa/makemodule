<?php


namespace Xuan\Makemodule\Console\ModuleInitCommands;


use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;

class MakeControllerCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:controller';
    protected $signature = 'make:controller {name} {module?} {init?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new controller class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Controller';

    protected $init = false;

    protected $baseName = '';

    protected $baseModule = '';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        if ($this->init) {
            return __DIR__ . '/Stubs/InitController.stub';
        } else {
            return __DIR__ . '/Stubs/Controller.stub';
        }
    }

    public function handle()
    {
        $this->init       = $this->arguments()['init'] ?? false;
        $this->baseName   = $this->arguments()['name'] ?? '';
        $this->baseModule = $this->arguments()['module'] ?? $this->baseName;
        $name             = $this->qualifyClass($this->getNameInput());
        $name             = $name . $this->type;

        $path = $this->getPath($name);

        // First we will check to see if the class already exists. If it does, we don't want
        // to create the class and overwrite the user's code. So, we will bail out so the
        // code is untouched. Otherwise, we will continue generating this class' files.
        if ((!$this->hasOption('force') ||
                !$this->option('force')) &&
            $this->alreadyExists($this->getNameInput())) {
            $this->error($this->type . ' already exists!');

            return false;
        }

        // Next, we will generate the path to the location where this class' file should get
        // written. Then, we will build the class and make the proper replacements on the
        // stub files so that it gets the correctly formatted namespace and class name.
        $this->makeDirectory($path);

        $this->files->put($path, $this->sortImports($this->buildClass($name)));

        $this->info($this->type . ' created successfully.');

        return true;
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string $rootNamespace
     *
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {

        return $rootNamespace . '\Modules\\' . $this->baseModule . '\Controllers';
    }

    protected function getPath($name)
    {
        $name = Str::replaceFirst($this->rootNamespace(), '', $name);

        return $this->laravel['path'] . '/' . str_replace('\\', '/', $name) . '.php';
    }

    protected function getServiceNamespace()
    {
        return $this->rootNamespace() . 'Modules\\' . $this->baseName . '\\Services\\' . $this->baseName . 'Service';
    }

    /**
     * Build the class with the given name.
     *
     * @param  string $name
     *
     * @return string
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function buildClass($name)
    {
        $stub = $this->files->get($this->getStub());
        if ($this->init) {
            $this->replaceModel($stub);
        }

        return $this->replaceNamespace($stub, $name)->replaceClass($stub, $name);
    }

    protected function replaceModel(&$stub)
    {
        $stub = str_replace(
            ['DummyServiceNamespace', 'DummyService'],
            [$this->getServiceNamespace(), $this->baseName . 'Service'],
            $stub
        );
    }
}