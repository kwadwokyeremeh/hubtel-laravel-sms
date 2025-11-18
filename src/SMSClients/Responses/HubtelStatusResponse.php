<?php

namespace NotificationChannels\Hubtel\SMSClients\Responses;

use Psr\Http\Message\ResponseInterface;

class HubtelStatusResponse extends HubtelBaseResponse
{
    /**
     * @var float|null
     */
    protected ?float $rate;

    /**
     * @var int|null
     */
    protected ?int $units;

    /**
     * @var string|null
     */
    protected ?string $messageId;

    /**
     * @var string|null
     */
    protected ?string $content;

    /**
     * @var string|null
     */
    protected ?string $status;

    /**
     * @var string|null
     */
    protected ?string $clientReference;

    /**
     * @var string|null
     */
    protected ?string $networkId;

    /**
     * @var string|null
     */
    protected ?string $updateTime;

    /**
     * @var string|null
     */
    protected ?string $time;

    /**
     * @var string|null
     */
    protected ?string $to;

    /**
     * @var string|null
     */
    protected ?string $from;

    /**
     * @param ResponseInterface $response
     * @param array $data
     */
    public function __construct(ResponseInterface $response, array $data = [])
    {
        parent::__construct($response);
        $this->rate = $data['rate'] ?? null;
        $this->units = $data['units'] ?? null;
        $this->messageId = $data['messageId'] ?? null;
        $this->content = $data['content'] ?? null;
        $this->status = $data['status'] ?? null;
        $this->clientReference = $data['clientReference'] ?? null;
        $this->networkId = $data['networkId'] ?? null;
        $this->updateTime = $data['updateTime'] ?? null;
        $this->time = $data['time'] ?? null;
        $this->to = $data['to'] ?? null;
        $this->from = $data['from'] ?? null;
    }

    /**
     * @return float|null
     */
    public function getRate(): ?float
    {
        return $this->rate;
    }

    /**
     * @return int|null
     */
    public function getUnits(): ?int
    {
        return $this->units;
    }

    /**
     * @return string|null
     */
    public function getMessageId(): ?string
    {
        return $this->messageId;
    }

    /**
     * @return string|null
     */
    public function getContent(): ?string
    {
        return $this->content;
    }

    /**
     * @return string|null
     */
    public function getStatus(): ?string
    {
        return $this->status;
    }

    /**
     * @return string|null
     */
    public function getClientReference(): ?string
    {
        return $this->clientReference;
    }

    /**
     * @return string|null
     */
    public function getNetworkId(): ?string
    {
        return $this->networkId;
    }

    /**
     * @return string|null
     */
    public function getUpdateTime(): ?string
    {
        return $this->updateTime;
    }

    /**
     * @return string|null
     */
    public function getTime(): ?string
    {
        return $this->time;
    }

    /**
     * @return string|null
     */
    public function getTo(): ?string
    {
        return $this->to;
    }

    /**
     * @return string|null
     */
    public function getFrom(): ?string
    {
        return $this->from;
    }

    /**
     * Check if the message was delivered successfully
     *
     * @return bool
     */
    public function isDelivered(): bool
    {
        return $this->status === 'Delivered';
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return [
            'rate' => $this->rate,
            'units' => $this->units,
            'messageId' => $this->messageId,
            'content' => $this->content,
            'status' => $this->status,
            'clientReference' => $this->clientReference,
            'networkId' => $this->networkId,
            'updateTime' => $this->updateTime,
            'time' => $this->time,
            'to' => $this->to,
            'from' => $this->from,
        ];
    }
}
