<?php

use Hussainabuhajjaj\Codebot\Services\DeepSeekService;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Mockery as m;

it("sends correct request to API", function () {
  $mockClient = m::mock(Client::class);
  $mockClient
    ->shouldReceive("post")
    ->with("/v1/completions", m::any())
    ->andReturn(
      new Response(
        200,
        [],
        json_encode([
          "choices" => [["text" => "test"]],
        ])
      )
    );

  $service = new DeepSeekService();
  $service->client = $mockClient;

  $response = $service->generateCode("test prompt");

  expect($response)->toBeArray();
});

it("handles API errors", function () {
  $mockClient = m::mock(Client::class);
  $mockClient
    ->shouldReceive("post")
    ->andThrow(
      new \GuzzleHttp\Exception\ClientException(
        "Error",
        m::mock(\Psr\Http\Message\RequestInterface::class),
        new Response(400)
      )
    );

  $service = new DeepSeekService();
  $service->client = $mockClient;

  $response = $service->generateCode("test prompt");
  expect($response)->toBeNull();
});
