<?php

use Hussainabuhajjaj\Codebot\Services\DeepSeekService;
use function Pest\Laravel\artisan;
use Mockery\MockInterface;

it('generates code interactively', function () {
    $this->mock(DeepSeekService::class, function (MockInterface $mock) {
        $mock->shouldReceive('generateCode')
            ->andReturn([
                'choices' => [
                    [
                        'text' => json_encode([
                            'migration' => 'test migration',
                            'model' => 'test model',
                            'views' => 'test views'
                        ])
                    ]
                ]
            ]);
    });

    artisan('codebot:generate')
        ->expectsQuestion('What is the name of the table?', 'posts')
        ->expectsConfirmation('Do you want to add a field?', 'yes')
        ->expectsQuestion('Enter the field name:', 'title')
        ->expectsQuestion('Select the field type:', 'string')
        ->expectsConfirmation('Is title nullable?', 'no')
        ->expectsConfirmation('Is title unique?', 'no')
        ->expectsConfirmation('Do you want to add a relationship?', 'no')
        ->expectsChoice('Select the CSS framework:', 'bootstrap', ['bootstrap', 'tailwind'])
        ->assertExitCode(0);
});

it('handles API failures gracefully', function () {
    $this->mock(DeepSeekService::class, function (MockInterface $mock) {
        $mock->shouldReceive('generateCode')->andReturnNull();
    });

    artisan('codebot:generate')
        ->expectsQuestion('What is the name of the table?', 'posts')
        ->expectsOutput('Error: Failed to generate code')
        ->assertExitCode(1);
});

it('validates table name format', function () {
    artisan('codebot:generate')
        ->expectsQuestion('What is the name of the table?', 'invalid table name!')
        ->expectsOutput('The name must only contain letters, numbers, dashes, and underscores.')
        ->assertExitCode(1);
});