<?php

namespace Hussainabuhajjaj\Codebot\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

class DeepSeekService
{
    protected Client $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => config('codebot.endpoint'),
            'headers' => [
                'Authorization' => 'Bearer '.config('codebot.api_key'),
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
            'timeout' => 15,
        ]);
    }

    public function generateCode(string $prompt): ?array
    {
        try {
            $response = $this->client->post('/v1/completions', [
                'json' => [
                    'model' => config('codebot.model'),
                    'prompt' => $prompt,
                    'max_tokens' => 2000,
                    'temperature' => 0.7,
                ]
            ]);

            return json_decode($response->getBody()->getContents(), true);

        } catch (GuzzleException $e) {
            Log::error('DeepSeek API Error: '.$e->getMessage());
            return null;
        }
    }
}