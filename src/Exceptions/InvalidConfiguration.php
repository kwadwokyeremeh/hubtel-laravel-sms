<?php

namespace NotificationChannels\Hubtel\Exceptions;

class InvalidConfiguration extends \Exception
{
    /**
     * @return static
     */
    public static function apiKeyNotSet(): static
    {
        return new static('Hubtel API key not set');
    }

    /**
     * @return static
     */
    public static function apiSecretNotSet(): static
    {
        return new static ('Hubtel API secret not set');
    }
}
