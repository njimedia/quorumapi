<?php

namespace NJIMedia\QuorumAPI;

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;

final class ClientTest extends TestCase
{
    public function testInvalidClientMissingArgs(): void
    {
        $this->expectException(\ArgumentCountError::class);
        $client = new Client();
    }

    public function testInvalidClientBadArgs(): void
    {
        $this->expectExceptionMessage('invalid');
        $badUsername = '\\$=';
        $badKey = '#@*%';

        // Mock response.
        $responseBody = 'Bad request. Invalid API key';
        $mock = new MockHandler([new Response('400', [], $responseBody)]);
        $handler = HandlerStack::create($mock);
        $mockClient = new GuzzleClient(['handler' => $handler]);
        $client = new Client($badUsername, $badKey, $mockClient);
    }

    public function testValidation(): void
    {
        // Mock response.
        $responseBody = '{}';
        $mock = new MockHandler([new Response('200', [], $responseBody)]);
        $handler = HandlerStack::create($mock);
        $mockClient = new GuzzleClient(['handler' => $handler]);
        $client = new Client('mockValidUsername', 'mockValidApiKey', $mockClient);
        $this->assertTrue($client->validate());
    }

    public function testGetCustomTags(): void
    {
        // Mock response.
        $data['meta']['model'] = 'CustomTag';
        $responseBody = json_encode($data, true);
        $mock = new MockHandler([new Response('200', [], $responseBody)]);
        $handler = HandlerStack::create($mock);
        $mockClient = new GuzzleClient(['handler' => $handler]);
        $client = new Client('mockValidUsername', 'mockValidApiKey', $mockClient);
        $response = $client->getCustomTags();
        // Status code should be '200'.
        $this->assertEquals(200, $response->getStatusCode());
        // Response body should contain a 'meta' prop, which has 'model' prop with string value 'CustomTag'.
        $this->assertEquals('CustomTag', json_decode($response->getBody(), true)['meta']['model']);
    }

    public function testGetLists(): void
    {
        // Mock response.
        $responseBody = "{}";
        $mock = new MockHandler([new Response('200', [], $responseBody)]);
        $handler = HandlerStack::create($mock);
        $mockClient = new GuzzleClient(['handler' => $handler]);
        $client = new Client('mockValidUsername', 'mockValidApiKey', $mockClient);
        $response = $client->getLists();
        // Status code should be '200'.
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testCreateSubscriber(): void
    {
        // Mock response.
        $responseBody = "{}";
        $mock = new MockHandler([new Response('201', [], $responseBody)]);
        $handler = HandlerStack::create($mock);
        $mockClient = new GuzzleClient(['handler' => $handler]);
        $client = new Client('mockValidUsername', 'mockValidApiKey', $mockClient);
        $data = [
            'firstname' => 'Test',
            'lastname'  => 'User',
            'email'     => 'mailbox@6c91a734bf.com',
        ];
        $response = $client->createSupporter($data);
        // Status code should be '201'.
        $this->assertEquals(201, $response->getStatusCode());
    }
}
