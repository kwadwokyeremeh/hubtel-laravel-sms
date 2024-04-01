<?php

namespace NotificationChannels\Hubtel\Exceptions;

class CouldNotSendNotification extends \Exception
{
    /**
     * @return static
     */
    public static function recipientNotSetError(): static
    {
        return new static('Recipient phone number not set');
    }

    /**
     * @return static
     */
    public static function senderNotSetError(): static
    {
        return new static('Sender phone number not set');
    }

    /**
     * @return static
     */
    public static function contentNotSetError(): static
    {
        return new static('Message content empty');
    }
}
