<?php

namespace NotificationChannels\Hubtel\SMSClients;

use GuzzleHttp\Client;
use NotificationChannels\Hubtel\Exceptions\InvalidConfiguration;

abstract class HubtelBaseClient
{
    /**
     * @var Client
     */
    protected Client $client;

    /**
     * @var string
     */
    protected string $apiKey;

    /**
     * @var string
     */
    protected string $apiSecret;

    /**
     * @param string $apiKey
     * @param string $apiSecret
     * @param Client $client
     */
    public function __construct(string $apiKey, string $apiSecret, Client $client)
    {
        $this->apiKey = $apiKey;
        $this->apiSecret = $apiSecret;
        $this->client = $client;
    }

    /**
     * Validate API configuration
     *
     * @param string $apiKey
     * @param string $apiSecret
     * @return $this
     * @throws InvalidConfiguration
     */
    protected function validateConfig(string $apiKey, string $apiSecret): static
    {
        if (empty($apiKey)) {
            throw InvalidConfiguration::apiKeyNotSet();
        }

        if (empty($apiSecret)) {
            throw InvalidConfiguration::apiSecretNotSet();
        }

        return $this;
    }

    /**
     * Get the base API URL
     *
     * @return string
     */
    abstract protected function getBaseApiURL(): string;

    /**
     * Parse the response from the API
     *
     * @param \Psr\Http\Message\ResponseInterface $response
     * @return mixed
     */
    abstract protected function parseResponse(\Psr\Http\Message\ResponseInterface $response): mixed;
}
