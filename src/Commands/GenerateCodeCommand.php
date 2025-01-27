<?php

namespace Hussainabuhajjaj\Codebot\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
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
    $fields = $this->collectFields();
    $relationships = $this->collectRelationships();
    $framework = $this->choice('Select the CSS framework:', ['bootstrap', 'tailwind']);

    $prompt = $this->buildPrompt($tableName, $fields, $relationships, $framework);
    $response = $this->deepSeekService->generateCode($prompt);

    $this->generateFiles($response, $tableName, $framework);
  }

  protected function collectFields() {
    $fields = [];
    while ($this->confirm('Do you want to add a field?')) {
      $fieldName = $this->ask('Enter the field name:');
      $fieldType = $this->choice('Select the field type:', ['string', 'integer', 'text', 'boolean', 'date']);
      $fields[] = [
        'name' => $fieldName,
        'type' => $fieldType,
        'nullable' => $this->confirm("Is $fieldName nullable?", false),
        'unique' => $this->confirm("Is $fieldName unique?", false),
        'default' => $this->ask("Default value for $fieldName (leave empty if none):", null),
      ];
    }
    return $fields;
  }

  protected function collectRelationships() {
    $relationships = [];
    while ($this->confirm('Do you want to add a relationship?')) {
      $relatedModel = $this->ask('Enter the related model name:');
      $relationshipType = $this->choice('Select the relationship type:', ['hasOne', 'hasMany', 'belongsTo', 'belongsToMany']);
      $foreignKey = $relationshipType === 'belongsTo'
      ? $this->ask("Foreign key column name for $relatedModel:", Str::snake($relatedModel) . '_id')
      : null;
      $onDelete = $relationshipType === 'belongsTo'
      ? $this->choice("Delete behavior for $relatedModel:", ['cascade', 'restrict', 'set null'])
      : null;
      $relationships[] = compact('relatedModel', 'relationshipType', 'foreignKey', 'onDelete');
    }
    return $relationships;
  }

  protected function buildPrompt(
    string $tableName,
    array $fields,
    array $relationships,
    string $framework
): string {
    $fieldDetails = $this->formatFields($fields);
    $relationshipDetails = $this->formatRelationships($relationships, $tableName);
    $validationRules = $this->formatValidationRules($fields);
    $frameworkInstructions = $this->getFrameworkSpecificInstructions($framework);

    $prompt = <<<PROMPT
Generate Laravel components following these strict requirements. 
Maintain exact syntax and structure. Use the latest Laravel conventions.

# Database Schema Requirements
## Table Structure
- Table name: {$tableName}
- Fields:
{$fieldDetails}

## Relationships
{$relationshipDetails}

# Model Requirements
- Namespace: App\Models
- Use proper PHPDoc blocks
- Include validation rules:
{$validationRules}
- Implement relationships:
{$relationshipDetails}

# Migration Requirements
- Use anonymous class structure
- Include proper indexes
- Foreign key constraints:
{$relationshipDetails}

# Views Requirements
- Framework: {$framework}
- Structure:
  - Index view: Search, Pagination, Responsive table
  - Create/Edit view: Form validation, Error display
  - Show view: Data card layout
- Include:
  - CSRF tokens
  - Accessible form labels
  - Proper semantic HTML
  - Success/error message display
  - Conditional loading states

# Security Requirements
- SQL injection protection
- Mass assignment protection
- Form request validation
- XSS protection for all user input

# Additional Instructions
- Use Laravel best practices
- Include comments for complex logic
- Follow PSR-12 coding standards
- Test coverage suggestions
- Support for localization
- Error handling for edge cases

{$frameworkInstructions}
PROMPT;

    return $prompt;
}
  protected function formatRelationships($relationships) {
    return collect($relationships)->map(fn($rel) => "- {$rel['relationshipType']} with {$rel['relatedModel']}")->implode("\n");
  }

  protected function generateFiles($response, $tableName, $framework) {
    $this->createFile('database/migrations', date('Y_m_d_His') . "_create_{$tableName}_table.php", $response['migration']);
    $this->createFile('app/Models', Str::studly($tableName) . '.php', $response['model']);
    $this->createFile("resources/views/$tableName", 'index.blade.php', $response['views']);
    $this->info('Code generated successfully!');
  }

  protected function createFile($path, $filename, $content) {
    if (!file_exists($path)) {
      mkdir($path, 0755, true);
    }
    file_put_contents("$path/$filename", $content);
  }
}