<?php

namespace Hussainabuhajjaj\Codebot;

use Illuminate\Support\ServiceProvider;

class AiGeneratorServiceProvider extends ServiceProvider
{
  public function boot() {
    // Publish configuration file
    $this->publishes([
      __DIR__.'/../config/codebot.php' => config_path('codebot.php'),
    ], 'config');

    // Publish templates
    $this->publishes([
      __DIR__.'/../templates' => resource_path('vendor/codebot/templates'),
    ], 'templates');

    // Register commands
    if ($this->app->runningInConsole()) {
      $this->commands([
        Commands\GenerateCodeCommand::class,
      ]);
    }
  }

  public function register() {
    // Merge config
    $this->mergeConfigFrom(
      __DIR__.'/../config/codebot.php', 'codebot'
    );
  }
}