<?php

namespace NotificationChannels\Hubtel\SMSClients\Responses;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class HubtelResponse extends HubtelBaseResponse
{
    /**
     * @var float|null
     */
    protected ?float $rate;

    /**
     * @var string|null
     */
    protected ?string $messageId;

    /**
     * @var int|null
     */
    protected ?int $status;

    /**
     * @var string|null
     */
    protected ?string $statusDescription;

    /**
     * @var string|null
     */
    protected ?string $networkId;

    /**
     * @param ResponseInterface $response
     * @param array $data
     */
    public function __construct(ResponseInterface $response, array $data = [])
    {
        parent::__construct($response);
        $this->rate = $data['rate'] ?? null;
        $this->messageId = $data['messageId'] ?? null;
        $this->status = $data['status'] ?? null;
        $this->statusDescription = $data['statusDescription'] ?? null;
        $this->networkId = $data['networkId'] ?? null;
    }

    /**
     * @return float|null
     */
    public function getRate(): ?float
    {
        return $this->rate;
    }

    /**
     * @return string|null
     */
    public function getMessageId(): ?string
    {
        return $this->messageId;
    }

    /**
     * @return int|null
     */
    public function getStatus(): ?int
    {
        return $this->status;
    }

    /**
     * @return string|null
     */
    public function getStatusDescription(): ?string
    {
        return $this->statusDescription;
    }

    /**
     * @return string|null
     */
    public function getNetworkId(): ?string
    {
        return $this->networkId;
    }

    /**
     * Check if the message was sent successfully
     *
     * @return bool
     */
    public function isSuccessful(): bool
    {
        return $this->status === 0;
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return [
            'rate' => $this->rate,
            'messageId' => $this->messageId,
            'status' => $this->status,
            'statusDescription' => $this->statusDescription,
            'networkId' => $this->networkId,
        ];
    }
}
