<?php

namespace NotificationChannels\Hubtel;

class HubtelMessage
{
    /**
     * Sender's phone number.
     * @var string
     */
    public mixed $from;

    /**
     * Recipient phone number.
     * @var string
     */
    public mixed $to;

    /**
     * Message.
     * @var string
     */
    public mixed $content;

    /**
     *Indicate a delivery report request.
     *@var bool
     */
    public bool $registeredDelivery;

    /**
     *The Reference Number provided by the Client
     *prior to sending the message.
     *@var int
     */
    public int $clientReference;

    /**
     * Indicates the type of message to be sent.
     *@var int
     */
    public int $type;

    /**
     *The User Data Header of the SMS Message being sent.
     *@var string
     */
    public string $udh;

    /**
     *Indicates when to send the message.
     *@var mixed
     */
    public mixed $time;

    /**
     *Indicates if this message must be sent as a flash message.
     *@var bool
     */
    public bool $flashMessage;

    public function __construct($from = null, $to = null, $content = null)
    {
        $this->from = $from;
        $this->to = $to;
        $this->content = $content;
    }

    /**
     * Set the message sender's phone number.
     * @param string $from
     * @return $this
     */
    public function from(string $from): static
    {
        $this->from = $from;

        return $this;
    }

    /**
     * Set the recipient's phone number.
     * @param string $to
     * @return $this
     */
    public function to(string $to): static
    {
        $this->to = $to;

        return $this;
    }

    /**
     * Set the message content.
     * @param string $content
     * @return $this
     */
    public function content(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    /**
     *Set delivery report status.
     * @return $this
     */
    public function registeredDelivery(): static
    {
        $this->registeredDelivery = 'true';

        return $this;
    }

    /**
     * Set the client reference number.
     * @param int $reference
     * @return $this
     */
    public function clientReference(int $reference): static
    {
        $this->clientReference = $reference;

        return $this;
    }

    /**
     * Set the message type.
     * @param int $type
     * @return $this
     */
    public function type(int $type): static
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Set the User Data Header of the SMS Message.
     * @param string $udh
     * @return $this
     */
    public function udh(string $udh): static
    {
        $this->udh = $udh;

        return $this;
    }

    /**
     * Set the time to send the message.
     * @param mixed $time
     * @return $this
     */
    public function time(mixed $time): static
    {
        $this->time = $time;

        return $this;
    }

    /**
     * Set message as a flash message.
     * @return $this
     */
    public function flashMessage(): static
    {
        $this->flashMessage = 'true';

        return $this;
    }
}
