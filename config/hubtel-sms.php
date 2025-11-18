<?php
/**
 * Hubtel SMS Channel Configuration
 *
 * This file holds the configuration keys for the Hubtel SMS channel.
 * Refer to the Hubtel SMS API documentation for detailed information about each setting.
 *
 * @see https://developers.hubtel.com/documentations/sendmessage
 */

return [
    /*
    |--------------------------------------------------------------------------
    | Hubtel API Key
    |--------------------------------------------------------------------------
    |
    | This is your Hubtel API Client ID. You can find this in your Hubtel account
    | under the Applications section. This is required for authentication with
    | the Hubtel SMS API.
    |
    | API Endpoint: https://smsc.hubtel.com/v1/messages/send
    |
    */
    'api_key' => env('HUBTEL_CLIENT_ID'),

    /*
    |--------------------------------------------------------------------------
    | Hubtel API Secret
    |--------------------------------------------------------------------------
    |
    | This is your Hubtel API Client Secret. You can find this in your Hubtel account
    | under the Applications section. This is required for authentication with
    | the Hubtel SMS API.
    |
    | Note: All endpoints are rate-limited, allowing up to 5 requests per minute.
    | For assistance with Sender ID approvals and top-ups, contact support@hubtel.com
    |
    */
    'api_secret' => env('HUBTEL_CLIENT_SECRET'),

    /*
    |--------------------------------------------------------------------------
    | Use POST Method for Sending
    |--------------------------------------------------------------------------
    |
    | Determines the HTTP method used for sending SMS messages:
    |
    | TRUE (default) - Uses POST request with Authorization header (Regular Send)
    | FALSE - Uses GET request with credentials in query parameters (Quick Send)
    |
    | Regular Send (POST):
    | - More secure as credentials are in Authorization header
    | - Request Type: POST
    | - Content Type: application/json
    | - Authorization: Basic base64(apiKey:apiSecret)
    | - Required Parameters: from, to, content
    |
    | Quick Send (GET):
    | - Less secure as credentials are in query parameters
    | - Request Type: GET
    | - Required Parameters: clientid, clientsecret, from, to, content
    |
    | Both methods return the same response format with rate, messageId, status,
    | statusDescription, and networkId fields.
    |
    */
    'use_post_method' => env('HUBTEL_SEND_METHOD', true),
];
