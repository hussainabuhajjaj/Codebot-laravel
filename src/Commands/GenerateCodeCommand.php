<?php

namespace Hussainabuhajjaj\Codebot\Commands;

use Illuminate\Console\Command;
use Hussainabuhajjaj\Codebot\Services\DeepSeekService;

class GenerateCodeCommand extends Command
{
  protected $signature = 'generate:code';
  protected $description = 'Generate migrations, models, and views interactively using DeepSeek AI';

  protected $deepSeekService;

  public function __construct(DeepSeekService $deepSeekService) {
    parent::__construct();
    $this->deepSeekService = $deepSeekService;
  }

  public function handle() {
    $tableName = $this->ask('What is the name of the table?');
    $fields = [];

    while ($this->confirm('Do you want to add a field?')) {
      $fieldName = $this->ask('Enter the field name:');
      $fieldType = $this->choice('Select the field type:', ['string', 'integer', 'text', 'boolean', 'date']);
      $fields[] = [
        'name' => $fieldName,
        'type' => $fieldType,
      ];
    }

    $relationships = [];
    while ($this->confirm('Do you want to add a relationship?')) {
      $relatedModel = $this->ask('Enter the related model name:');
      $relationshipType = $this->choice('Select the relationship type:', ['hasOne', 'hasMany', 'belongsTo', 'belongsToMany']);
      $relationships[] = [
        'model' => $relatedModel,
        'type' => $relationshipType,
      ];
    }

    $framework = $this->choice('Select the CSS framework:', ['bootstrap', 'tailwind']);

    $prompt = $this->buildPrompt($tableName, $fields, $relationships, $framework);
    $response = $this->deepSeekService->generateCode($prompt);

    $this->generateFiles($response, $tableName, $framework);
  }

  protected function buildPrompt($tableName, $fields, $relationships, $framework) {
    $prompt = "Generate Laravel migration, model, and views for a table named '$tableName' with the following fields:\n";
    foreach ($fields as $field) {
      $prompt .= "- {$field['name']} ({$field['type']})\n";
    }

    if (!empty($relationships)) {
      $prompt .= "\nWith the following relationships:\n";
      foreach ($relationships as $relationship) {
        $prompt .= "- {$relationship['type']} with {$relationship['model']}\n";
      }
    }

    $prompt .= "\nUse $framework for the views.";

    return $prompt;
  }

  protected function generateFiles($response, $tableName, $framework) {
    // Generate Migration
    $migrationTemplate = $this->getTemplate('migration');
    $migrationCode = str_replace('{{table}}', $tableName, $migrationTemplate);
    $migrationCode = str_replace('{{fields}}', $response['migration'], $migrationCode);
    $this->createFile('database/migrations', date('Y_m_d_His') . "_create_{$tableName}_table.php", $migrationCode);

    // Generate Model
    $modelTemplate = $this->getTemplate('model');
    $modelCode = str_replace('{{table}}', $tableName, $modelTemplate);
    $modelCode = str_replace('{{fields}}', $response['model'], $modelCode);
    $this->createFile('app/Models', ucfirst($tableName) . '.php', $modelCode);

    // Generate Views
    $viewTemplate = $this->getTemplate('view');
    $viewCode = str_replace('{{table}}', $tableName, $viewTemplate);
    $viewCode = str_replace('{{framework}}', $framework, $viewCode);
    $this->createFile('resources/views', "$tableName/index.blade.php", $viewCode);

    $this->info('Code generated successfully!');
  }

  protected function getTemplate($type) {
    $customTemplatePath = resource_path("vendor/codebot/templates/$type.stub");
    if (file_exists($customTemplatePath)) {
      return file_get_contents($customTemplatePath);
    }

    return file_get_contents(__DIR__."/../templates/$type.stub");
  }

  protected function createFile($path, $filename, $content) {
    if (!file_exists($path)) {
      mkdir($path, 0755, true);
    }

    file_put_contents("$path/$filename", $content);
  }
}