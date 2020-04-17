<?php


namespace Xuan\Makemodule\Console\ModuleInitCommands;


use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;

class MakeEloquentModelCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:eloquent';
    protected $signature = 'make:eloquent {name} {module?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new eloquent class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Model';

    protected $baseName = '';

    protected $moduleName = '';

    public function handle()
    {
        $this->baseName   = $this->argument('name');
        $this->moduleName = $this->arguments()['module'] ?? $this->baseName;
        $name             = $this->qualifyClass($this->getNameInput());
        $name             = $name . $this->type;
        $path             = $this->getPath($name);

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
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__ . '/Stubs/EloquentModel.stub';
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

        return $rootNamespace . '\Modules\\' . $this->moduleName . '\Models';
    }

    protected function getPath($name)
    {
        $name = Str::replaceFirst($this->rootNamespace(), '', $name);

        return $this->laravel['path'] . '/' . str_replace('\\', '/', $name) . '.php';
    }
}