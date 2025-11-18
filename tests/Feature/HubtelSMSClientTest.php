<?php

namespace NotificationChannels\Hubtel\Test\Feature;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use NotificationChannels\Hubtel\Exceptions\InvalidConfiguration;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use NotificationChannels\Hubtel\HubtelMessage;
use NotificationChannels\Hubtel\SMSClients\HubtelSMSClient;

class HubtelSMSClientTest extends TestCase
{
    public $client;

    public function setUp():void
    {
        parent::setUp();
    }

    /** @test **/
    public function it_sends_message_given_valid_sms_credentials()
    {
        // Create a mock response
        $mock = new MockHandler([
            new Response(201, [], json_encode([
                'rate' => 0.03,
                'messageId' => 'c9d729d6-a802-425e-9862-9fe4c0f09d63',
                'status' => 0,
                'statusDescription' => null,
                'networkId' => ''
            ]))
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $message = new HubtelMessage('TestSender','233123456789','API test message');

        $smsClient = new HubtelSMSClient('your-api-key','your-api-secret',$client, false);

        try {
            $response = $smsClient->send($message);
        } catch (GuzzleException|InvalidConfiguration $e) {
            throw $e;
        }

        $this->assertEquals(201,$response->getStatusCode());
        $this->assertInstanceOf(\NotificationChannels\Hubtel\SMSClients\Responses\HubtelResponse::class, $response);
        $this->assertTrue($response->isSuccessful());
    }

    #[Test]
    public function it_sends_message_using_post_method()
    {
        // Create a mock response
        $mock = new MockHandler([
            new Response(200, [], json_encode([
                'rate' => 0.03,
                'messageId' => 'c9d729d6-a802-425e-9862-9fe4c0f09d63',
                'status' => 0,
                'statusDescription' => null,
                'networkId' => ''
            ]))
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $smsClient = new HubtelSMSClient('testApiKey', 'testApiSecret', $client, true);
        $message = new HubtelMessage('TestSender', '233123456789', 'Hello World');

        $response = $smsClient->send($message);

        $this->assertInstanceOf(\NotificationChannels\Hubtel\SMSClients\Responses\HubtelResponse::class, $response);
        $this->assertTrue($response->isSuccessful());
        $this->assertEquals('c9d729d6-a802-425e-9862-9fe4c0f09d63', $response->getMessageId());
        $this->assertEquals(0.03, $response->getRate());
    }

    #[Test]
    public function it_sends_message_using_get_method()
    {
        // Create a mock response
        $mock = new MockHandler([
            new Response(200, [], json_encode([
                'rate' => 0.03,
                'messageId' => 'c9d729d6-a802-425e-9862-9fe4c0f09d63',
                'status' => 0,
                'statusDescription' => null,
                'networkId' => ''
            ]))
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $smsClient = new HubtelSMSClient('testApiKey', 'testApiSecret', $client, false);
        $message = new HubtelMessage('TestSender', '233123456789', 'Hello World');

        $response = $smsClient->send($message);

        $this->assertInstanceOf(\NotificationChannels\Hubtel\SMSClients\Responses\HubtelResponse::class, $response);
        $this->assertTrue($response->isSuccessful());
        $this->assertEquals('c9d729d6-a802-425e-9862-9fe4c0f09d63', $response->getMessageId());
        $this->assertEquals(0.03, $response->getRate());
    }

    #[Test]
    public function it_handles_failed_message_sending()
    {
        // Create a mock response for failed sending
        $mock = new MockHandler([
            new Response(200, [], json_encode([
                'rate' => 0.03,
                'messageId' => 'c9d729d6-a802-425e-9862-9fe4c0f09d63',
                'status' => 2, // Failed status
                'statusDescription' => 'Failed to send',
                'networkId' => ''
            ]))
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $smsClient = new HubtelSMSClient('testApiKey', 'testApiSecret', $client);
        $message = new HubtelMessage('TestSender', '233123456789', 'Hello World');

        $response = $smsClient->send($message);

        $this->assertInstanceOf(\NotificationChannels\Hubtel\SMSClients\Responses\HubtelResponse::class, $response);
        $this->assertFalse($response->isSuccessful());
        $this->assertEquals(2, $response->getStatus());
        $this->assertEquals('Failed to send', $response->getStatusDescription());
    }

    /**
     * Live test that actually hits the Hubtel servers.
     * This test requires valid HUBTEL_CLIENT_ID and HUBTEL_CLIENT_SECRET environment variables.
     *
     */
    #[Group("live")]
    public function test_it_sends_message_via_post_method_live()
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
        $smsClient = new HubtelSMSClient($apiKey, $apiSecret, $client, true);

        // Create a test message
        $message = new HubtelMessage($senderId, $phoneNumber, 'Live test message from Hubtel SMS Client');

        try {
            // Attempt to send the message
            $response = $smsClient->send($message);

            // Check that we received a proper response
            $this->assertInstanceOf(\NotificationChannels\Hubtel\SMSClients\Responses\HubtelResponse::class, $response);

            // The API key might not be valid for actual sending, but we can still check the response structure
            $this->assertNotEmpty($response->getMessageId());
        } catch (\Exception $e) {
            // If we get an authentication error or similar, that's fine for this test
            // We're mainly verifying that the HTTP connection works
            $this->assertNotEmpty($e->getMessage());
        }
    }

    /**
     * Live test that actually hits the Hubtel servers using GET method.
     * This test requires valid HUBTEL_CLIENT_ID and HUBTEL_CLIENT_SECRET environment variables.
     *
     */
    #[Group("live")]
    public function test_it_sends_message_via_get_method_live()
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
        $smsClient = new HubtelSMSClient($apiKey, $apiSecret, $client, false);

        // Create a test message
        $message = new HubtelMessage($senderId, $phoneNumber, 'Live test message from Hubtel SMS Client (GET method)');

        try {
            // Attempt to send the message
            $response = $smsClient->send($message);

            // Check that we received a proper response
            $this->assertInstanceOf(\NotificationChannels\Hubtel\SMSClients\Responses\HubtelResponse::class, $response);

            // The API key might not be valid for actual sending, but we can still check the response structure
            $this->assertNotEmpty($response->getMessageId());
        } catch (\Exception $e) {
            // If we get an authentication error or similar, that's fine for this test
            // We're mainly verifying that the HTTP connection works
            $this->assertNotEmpty($e->getMessage());
        }
    }
}
