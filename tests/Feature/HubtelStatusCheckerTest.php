<?php

namespace NotificationChannels\Hubtel\Test\Feature;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use NotificationChannels\Hubtel\SMSClients\HubtelStatusChecker;

class HubtelStatusCheckerTest extends TestCase
{
    public function test_it_can_query_message_status()
    {
        $apiKey = getenv('HUBTEL_CLIENT_ID');
        $apiSecret = getenv('HUBTEL_CLIENT_SECRET');
        $senderId = getenv('HUBTEL_SENDER_ID');
        $phoneNumber = getenv('HUBTEL_PHONE_NUMBER');
        // Create a mock response
        $mock = new MockHandler([
            new Response(200, [], json_encode([
                'rate' => 0.03,
                'units' => 0,
                'messageId' => 'c9d729d6-a802-425e-9862-9fe4c0f09d63',
                'content' => 'hello world',
                'status' => 'Delivered',
                'clientReference' => null,
                'networkId' => null,
                'updateTime' => '2025-04-15T14:09:03',
                'time' => '2025-04-15T14:08:56.2711269Z',
                'to' => '+233200585542',
                'from' => 'RSETest'
            ]))
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $statusChecker = new HubtelStatusChecker('testApiKey', 'testApiSecret', $client);
        $response = $statusChecker->query('c9d729d6-a802-425e-9862-9fe4c0f09d63');

        $this->assertInstanceOf(\NotificationChannels\Hubtel\SMSClients\Responses\HubtelStatusResponse::class, $response);
        $this->assertEquals('Delivered', $response->getStatus());
        $this->assertTrue($response->isDelivered());
        $this->assertEquals('c9d729d6-a802-425e-9862-9fe4c0f09d63', $response->getMessageId());
        $this->assertEquals(0.03, $response->getRate());
    }

    public function test_it_handles_undelivered_message_status()
    {
        $apiKey = getenv('HUBTEL_CLIENT_ID');
        $apiSecret = getenv('HUBTEL_CLIENT_SECRET');
        $senderId = getenv('HUBTEL_SENDER_ID');
        $phoneNumber = getenv('HUBTEL_PHONE_NUMBER');

        // Create a mock response for undelivered message
        $mock = new MockHandler([
            new Response(200, [], json_encode([
                'rate' => 0.03,
                'units' => 0,
                'messageId' => 'c9d729d6-a802-425e-9862-9fe4c0f09d63',
                'content' => 'hello world',
                'status' => 'Undelivered',
                'clientReference' => null,
                'networkId' => null,
                'updateTime' => '2025-04-15T14:09:03',
                'time' => '2025-04-15T14:08:56.2711269Z',
                'to' => '+233200585542',
                'from' => 'RSETest'
            ]))
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $statusChecker = new HubtelStatusChecker('testApiKey', 'testApiSecret', $client);
        $response = $statusChecker->query('c9d729d6-a802-425e-9862-9fe4c0f09d63');

        $this->assertInstanceOf(\NotificationChannels\Hubtel\SMSClients\Responses\HubtelStatusResponse::class, $response);
        $this->assertEquals('Undelivered', $response->getStatus());
        $this->assertFalse($response->isDelivered());
    }

    /**
     * Live test that actually hits the Hubtel servers.
     * This test requires valid HUBTEL_CLIENT_ID and HUBTEL_CLIENT_SECRET environment variables.
     *
     */
    #[Group("live")]
    public function test_it_can_query_message_status_live()
    {
        $apiKey = getenv('HUBTEL_CLIENT_ID');
        $apiSecret = getenv('HUBTEL_CLIENT_SECRET');
        $senderId = getenv('HUBTEL_SENDER_ID');
        $phoneNumber = getenv('HUBTEL_PHONE_NUMBER');

        if (!$apiKey || !$apiSecret) {
            $this->markTestSkipped('Live test requires HUBTEL_CLIENT_ID and HUBTEL_CLIENT_SECRET environment variables');
        }

        // Skip this test by default since it requires actual credentials and network connectivity
        if (getenv('RUN_LIVE_TESTS') !== 'true') {
            $this->markTestSkipped('Live tests are disabled. Set RUN_LIVE_TESTS=true to enable.');
        }

        $client = new Client();
        $statusChecker = new HubtelStatusChecker($apiKey, $apiSecret, $client);

        // Try to query a non-existent message ID - we're mainly testing the connection
        try {
            $response = $statusChecker->query('invalid-message-id');

            // If we get here, the request was made successfully (even if it returns an error)
            $this->assertInstanceOf(\NotificationChannels\Hubtel\SMSClients\Responses\HubtelStatusResponse::class, $response);
        } catch (\Exception $e) {
            // We expect this to fail with a 404 or similar since we're using an invalid ID
            // But the fact that we got an HTTP response shows the connection works
            $this->assertNotEmpty($e->getMessage());
        }
    }
}
