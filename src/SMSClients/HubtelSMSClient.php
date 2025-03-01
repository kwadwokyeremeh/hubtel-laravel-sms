<?php

namespace NotificationChannels\Hubtel\SMSClients;

use GuzzleHttp\Client;
use NotificationChannels\Hubtel\Exceptions\InvalidConfiguration;
use NotificationChannels\Hubtel\HubtelMessage;

class HubtelSMSClient
{
    /**
     * @var Client
     */
    public Client $client;

    /**
     * @var string
     */
    public string $apiKey;

    /**
     * @var string
     */
    public string $apiSecret;

    /**
     * @param $apiKey
     * @param $apiSecret
     * @param Client $client
     */
    public function __construct($apiKey, $apiSecret, Client $client)
    {
        $this->apiKey = $apiKey;
        $this->apiSecret = $apiSecret;
        $this->client = $client;
    }

    /**
     * @param HubtelMessage $message
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws InvalidConfiguration
     */
    public function send(HubtelMessage $message): \Psr\Http\Message\ResponseInterface
    {
        return $this->client->get($this->getApiURL().$this->buildMessage($message, $this->apiKey, $this->apiSecret));
    }

    public function getApiURL(): string
    {
        return 'https://smsc.hubtel.com/v1/messages/send?';
    }

    /**
     * @param HubtelMessage $message
     * @param $apiKey
     * @param $apiSecret
     * @return string
     * @throws InvalidConfiguration
     */
    public function buildMessage(HubtelMessage $message, $apiKey, $apiSecret): string
    {
        $this->validateConfig($apiKey, $apiSecret);

        $params = ['ClientId'=>$apiKey, 'ClientSecret' => $apiSecret];

        foreach (get_object_vars($message) as $property => $value) {
            if (! is_null($value)) {
                $params[ucfirst($property)] = $value;
            }
        }

        return http_build_query($params);
    }

    /**
     * @param $apiKey
     * @param $apiSecret
     * @return $this
     * @throws InvalidConfiguration
     */
    public function validateConfig($apiKey, $apiSecret): static
    {
        if (is_null($apiKey)) {
            throw InvalidConfiguration::apiKeyNotSet();
        }

        if (is_null($apiSecret)) {
            throw InvalidConfiguration::apiSecretNotSet();
        }

        return $this;
    }
}
