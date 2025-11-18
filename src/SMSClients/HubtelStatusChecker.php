<?php

namespace NotificationChannels\Hubtel\SMSClients;

use GuzzleHttp\Client;
use NotificationChannels\Hubtel\Exceptions\InvalidConfiguration;
use NotificationChannels\Hubtel\SMSClients\Responses\HubtelStatusResponse;

class HubtelStatusChecker extends HubtelBaseClient
{
    /**
     * @param string $apiKey
     * @param string $apiSecret
     * @param Client $client
     */
    public function __construct(string $apiKey, string $apiSecret, Client $client)
    {
        parent::__construct($apiKey, $apiSecret, $client);
    }

    /**
     * Query the status of a previously sent message
     *
     * @param string $messageId
     * @return HubtelStatusResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws InvalidConfiguration
     */
    public function query(string $messageId): HubtelStatusResponse
    {
        $this->validateConfig($this->apiKey, $this->apiSecret);

        $auth = base64_encode($this->apiKey . ':' . $this->apiSecret);

        $response = $this->client->get($this->getStatusApiURL($messageId), [
            'headers' => [
                'Authorization' => 'Basic ' . $auth,
                'Content-Type' => 'application/json',
            ],
        ]);

        return $this->parseResponse($response);
    }

    /**
     * Get the API URL for querying message status
     *
     * @param string $messageId
     * @return string
     */
    protected function getStatusApiURL(string $messageId): string
    {
        return $this->getBaseApiURL() . $messageId;
    }

    /**
     * @inheritDoc
     */
    protected function getBaseApiURL(): string
    {
        return 'https://smsc.hubtel.com/v1/messages/';
    }

    /**
     * @inheritDoc
     */
    protected function parseResponse(\Psr\Http\Message\ResponseInterface $response): HubtelStatusResponse
    {
        $body = (string) $response->getBody();
        $data = json_decode($body, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException('Unable to parse response body into JSON: ' . json_last_error_msg());
        }

        return new HubtelStatusResponse($response, $data ?: []);
    }
}
