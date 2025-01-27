<?php

namespace Hussainabuhajjaj\Codebot\Services;

use GuzzleHttp\Client;

class DeepSeekService
{
    protected Client $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => config('codebot.deepseek_endpoint'),
            'headers' => [
                'Authorization' => 'Bearer '.config('codebot.deepseek_api_key'),
                'Content-Type' => 'application/json',
            ]
        ]);
    }

    public function generateCode(string $prompt): string
    {
        $response = $this->client->post('/v1/generate', [
            'json' => ['prompt' => $prompt]
        ]);

        return json_decode($response->getBody(), true)['code'];
    }
}