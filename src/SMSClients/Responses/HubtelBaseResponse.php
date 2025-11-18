<?php

namespace NotificationChannels\Hubtel\SMSClients\Responses;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

abstract class HubtelBaseResponse implements ResponseInterface
{
    /**
     * @var ResponseInterface
     */
    protected ResponseInterface $response;

    /**
     * @param ResponseInterface $response
     */
    public function __construct(ResponseInterface $response)
    {
        $this->response = $response;
    }

    /**
     * Get the response data as an array
     *
     * @return array
     */
    abstract public function toArray(): array;

    // Implementation of ResponseInterface methods

    public function getProtocolVersion(): string
    {
        return $this->response->getProtocolVersion();
    }

    public function withProtocolVersion($version): ResponseInterface
    {
        $new = clone $this;
        $new->response = $this->response->withProtocolVersion($version);
        return $new;
    }

    public function getHeaders(): array
    {
        return $this->response->getHeaders();
    }

    public function hasHeader($name): bool
    {
        return $this->response->hasHeader($name);
    }

    public function getHeader($name): array
    {
        return $this->response->getHeader($name);
    }

    public function getHeaderLine($name): string
    {
        return $this->response->getHeaderLine($name);
    }

    public function withHeader($name, $value): ResponseInterface
    {
        $new = clone $this;
        $new->response = $this->response->withHeader($name, $value);
        return $new;
    }

    public function withAddedHeader($name, $value): ResponseInterface
    {
        $new = clone $this;
        $new->response = $this->response->withAddedHeader($name, $value);
        return $new;
    }

    public function withoutHeader($name): ResponseInterface
    {
        $new = clone $this;
        $new->response = $this->response->withoutHeader($name);
        return $new;
    }

    public function getBody(): StreamInterface
    {
        return $this->response->getBody();
    }

    public function withBody(StreamInterface $body): ResponseInterface
    {
        $new = clone $this;
        $new->response = $this->response->withBody($body);
        return $new;
    }

    public function getStatusCode(): int
    {
        return $this->response->getStatusCode();
    }

    public function withStatus($code, $reasonPhrase = ''): ResponseInterface
    {
        $new = clone $this;
        $new->response = $this->response->withStatus($code, $reasonPhrase);
        return $new;
    }

    public function getReasonPhrase(): string
    {
        return $this->response->getReasonPhrase();
    }
}
