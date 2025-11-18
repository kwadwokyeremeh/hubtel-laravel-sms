<?php

namespace NotificationChannels\Hubtel\SMSClients;

use GuzzleHttp\Client;
use NotificationChannels\Hubtel\Exceptions\InvalidConfiguration;
use NotificationChannels\Hubtel\HubtelMessage;
use NotificationChannels\Hubtel\SMSClients\Responses\HubtelResponse;

class HubtelSMSClient extends HubtelBaseClient
{
    /**
     * @var bool
     */
    public bool $usePostMethod;

    /**
     * @param string $apiKey
     * @param string $apiSecret
     * @param Client $client
     * @param bool $usePostMethod
     */
    public function __construct(string $apiKey, string $apiSecret, Client $client, bool $usePostMethod = true)
    {
        parent::__construct($apiKey, $apiSecret, $client);
        $this->usePostMethod = $usePostMethod;
    }

    /**
     * @param HubtelMessage $message
     * @return HubtelResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws InvalidConfiguration
     */
    public function send(HubtelMessage $message): HubtelResponse
    {
        if ($this->usePostMethod) {
            $response = $this->sendViaPost($message);
        } else {
            $response = $this->sendViaGet($message);
        }

        return $this->parseResponse($response);
    }

    /**
     * Send SMS using POST method with Authorization header (Regular Send)
     *
     * @param HubtelMessage $message
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws InvalidConfiguration
     */
    protected function sendViaPost(HubtelMessage $message): \Psr\Http\Message\ResponseInterface
    {
        $this->validateConfig($this->apiKey, $this->apiSecret);

        $payload = [];
        foreach (get_object_vars($message) as $property => $value) {
            if (! is_null($value)) {
                $payload[$property] = $value;
            }
        }

        $auth = base64_encode($this->apiKey . ':' . $this->apiSecret);

        return $this->client->post($this->getBaseApiURL(), [
            'headers' => [
                'Authorization' => 'Basic ' . $auth,
                'Content-Type' => 'application/json',
            ],
            'json' => $payload,
        ]);
    }

    /**
     * Send SMS using GET method with credentials in query params (Quick Send)
     *
     * @param HubtelMessage $message
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws InvalidConfiguration
     */
    protected function sendViaGet(HubtelMessage $message): \Psr\Http\Message\ResponseInterface
    {
        return $this->client->get($this->getBaseApiURL().'?'.$this->buildMessage($message, $this->apiKey, $this->apiSecret));
    }

    /**
     * @inheritDoc
     */
    protected function getBaseApiURL(): string
    {
        return 'https://smsc.hubtel.com/v1/messages/send';
    }

    /**
     * @param HubtelMessage $message
     * @param string $apiKey
     * @param string $apiSecret
     * @return string
     * @throws InvalidConfiguration
     */
    public function buildMessage(HubtelMessage $message, string $apiKey, string $apiSecret): string
    {
        $this->validateConfig($apiKey, $apiSecret);

        $params = ['clientid' => $apiKey, 'clientsecret' => $apiSecret];

        foreach (get_object_vars($message) as $property => $value) {
            if (! is_null($value)) {
                // Convert property names to lowercase to match API requirements for GET requests
                $params[strtolower($property)] = $value;
            }
        }

        return http_build_query($params);
    }

    /**
     * @inheritDoc
     */
    protected function parseResponse(\Psr\Http\Message\ResponseInterface $response): HubtelResponse
    {
        $body = (string) $response->getBody();
        $data = json_decode($body, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException('Unable to parse response body into JSON: ' . json_last_error_msg());
        }

        return new HubtelResponse($response, $data ?: []);
    }
}
