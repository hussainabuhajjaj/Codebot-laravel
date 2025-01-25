<?php

   namespace Hussainabuhajjaj\Codebot\Services;

   use GuzzleHttp\Client;

   class DeepSeekService
   {
       protected $client;
       protected $apiKey;

       public function __construct()
       {
           $this->client = new Client([
               'base_uri' => 'https://api.deepseek.com/v1/',
               'headers' => [
                   'Authorization' => 'Bearer ' . config('codebot.deepseek_api_key'),
                   'Content-Type' => 'application/json',
               ],
           ]);
       }

       public function generateCode($prompt)
       {
           $response = $this->client->post('generate', [
               'json' => [
                   'prompt' => $prompt,
               ],
           ]);

           return json_decode($response->getBody(), true);
       }
   }