<?php

namespace Xuan\Makemodule\Console\ModuleInitCommands;

use Illuminate\Console\Command;

class InitModule extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:module {module?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';
    const MODULE_PATH = 'app/Modules/';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $_module = $this->arguments()['module'] ?? null;
        if (is_null($_module)) {
            $_module = $this->ask('请输入需要初始化的模块');
        }
        if (empty($_module)) {
            $this->error('模块为空，bye～');

            return false;
        }
        if (is_dir(self::MODULE_PATH . $_module)) {
            $this->error($_module . '模块已存在，请勿重复创建');
        } else {
            $this->comment('开始新建模块');
            exec('mkdir  ' . self::MODULE_PATH . $_module);
            exec('mkdir  ' . self::MODULE_PATH . $_module . '/Services/');
            exec('mkdir  ' . self::MODULE_PATH . $_module . '/Repositories/');
            exec('mkdir  ' . self::MODULE_PATH . $_module . '/Models/');
            exec('mkdir  ' . self::MODULE_PATH . $_module . '/Controllers/');
            exec('mkdir  ' . self::MODULE_PATH . $_module . '/Providers/');
            exec('touch ' . self::MODULE_PATH . $_module . '/routes.php');
            $this->comment('模块文件夹创建完成，开始初始化类');
            $this->comment('开始初始化Model');
            exec('php artisan make:eloquent ' . $_module);
            $this->comment('初始化Model完成');
            $this->comment('开始初始化Repository');
            exec('php artisan make:repository ' . $_module . ' ' . $_module . ' true');
            $this->comment('初始化Repository完成');
            $this->comment('开始初始化Service');
            exec('php artisan make:service ' . $_module . ' ' . $_module . ' true');
            $this->comment('初始化Service完成');
            $this->comment('开始初始化Controller');
            exec('php artisan make:controller ' . $_module . ' ' . $_module . ' true');
            $this->comment('初始化Controller完成');
            $this->comment('开始初始化ServiceProvider');
            exec('php artisan make:service_provider ' . $_module);
            $this->comment('初始化ServiceProvider完成');
            $this->comment('初始化模块完成，请去AppServiceProvider中注册模块ServiceProvider，Bye～  ：）');
        }
//
    }


}
