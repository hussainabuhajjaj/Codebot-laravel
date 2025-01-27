<?php

return [
    'api_key' => env('DEEPSEEK_API_KEY'),
    'endpoint' => env('DEEPSEEK_ENDPOINT', 'https://api.deepseek.com/v1/completions'),
    'model' => env('DEEPSEEK_MODEL', 'code-davinci-002'),
    
    'defaults' => [
        'framework' => 'bootstrap',
        'validation' => true,
        'relationships' => true,
    ],
    
    'paths' => [
        'migrations' => database_path('migrations'),
        'models' => app_path('Models'),
        'views' => resource_path('views'),
    ],
    
    'security' => [
        'mass_assignment_guarded' => true,
        'csrf_protection' => true,
        'xss_filtering' => true,
    ]
];