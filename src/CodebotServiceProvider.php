<?php

namespace Hussainabuhajjaj\Codebot;

use Illuminate\Support\ServiceProvider;
use Hussainabuhajjaj\Codebot\Commands\GenerateCodeCommand;

class CodebotServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->publishConfig();
        $this->publishTemplates();
        $this->registerCommands();
    }

    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/codebot.php',
            'codebot'
        );
    }

    protected function publishConfig(): void
    {
        $this->publishes([
            __DIR__.'/../config/codebot.php' => config_path('codebot.php'),
        ], 'codebot-config');
    }

    protected function publishTemplates(): void
    {
        $this->publishes([
            __DIR__.'/../templates' => resource_path('vendor/codebot/templates'),
        ], 'codebot-templates');
    }

    protected function registerCommands(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                GenerateCodeCommand::class,
            ]);
        }
    }
}