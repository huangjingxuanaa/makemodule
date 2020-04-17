<?php


namespace Xuan\Makemodule;

use Illuminate\Support\ServiceProvider;

class MakeModuleServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {

            $this->commands([
                Console\ModuleInitCommands\InitModule::class,
                Console\ModuleInitCommands\MakeServiceCommand::class,
                Console\ModuleInitCommands\MakeRepositoryCommand::class,
                Console\ModuleInitCommands\MakeEloquentModelCommand::class,
                Console\ModuleInitCommands\MakeControllerCommand::class,
                Console\ModuleInitCommands\MakeServiceProviderCommand::class,
            ]);
        }
    }
}